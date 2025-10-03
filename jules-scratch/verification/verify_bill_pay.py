from playwright.sync_api import sync_playwright

def run(playwright):
    browser = playwright.chromium.launch(headless=True)
    context = browser.new_context()
    page = context.new_page()

    # Login
    page.goto("http://localhost:8000/login.php")
    page.locator('input[name="username"]').fill("admin")
    page.locator('input[name="password"]').fill("admin")
    page.locator('button[type="submit"]').click()

    # Navigate to bills.php
    page.goto("http://localhost:8000/bills.php")

    # --- Verify 'Bill' action ---
    # Start waiting for the popup before clicking
    with page.expect_popup() as popup_info:
        page.locator("text=Bill").first.click()
    bill_page = popup_info.value
    bill_page.wait_for_load_state()

    # Take a screenshot of the invoice page
    bill_page.screenshot(path="jules-scratch/verification/invoice_page.png")
    bill_page.close()


    # --- Verify 'Pay' action ---
    # Start waiting for the popup before clicking
    with page.expect_popup() as popup_info:
        page.locator("text=Pay").first.click()
    pay_page = popup_info.value
    pay_page.wait_for_load_state()

    # Take a screenshot of the "Notice of Disconnection" page
    pay_page.screenshot(path="jules-scratch/verification/pay_page.png")
    pay_page.close()

    browser.close()

with sync_playwright() as playwright:
    run(playwright)