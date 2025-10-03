from playwright.sync_api import sync_playwright

def run(playwright):
    browser = playwright.chromium.launch(headless=True)
    context = browser.new_context()
    page = context.new_page()

    # Log in
    page.goto("http://0.0.0.0:8000/login.php")
    page.fill("input[name='user_name']", "admin")
    page.fill("input[name='password']", "12345678")
    page.click("button[name='btn-login']")

    # Go to bills page
    page.goto("http://0.0.0.0:8000/bills.php")

    # Take screenshot
    page.screenshot(path="jules-scratch/verification/bills.png")

    browser.close()

with sync_playwright() as playwright:
    run(playwright)