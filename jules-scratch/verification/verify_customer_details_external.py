from playwright.sync_api import sync_playwright, expect
import time

def run(playwright):
    browser = playwright.chromium.launch()
    context = browser.new_context()
    page = context.new_page()

    try:
        # Log in as admin
        page.goto("https://interactivequiz.free.nf/login.php")
        page.fill("input[name='username']", "admin")
        page.fill("input[name='password']", "12345678")
        page.click("button[type='submit']")

        # Wait for navigation to the index page
        page.wait_for_url("https://interactivequiz.free.nf/index.php")

        # Go to the customers page
        page.goto("https://interactivequiz.free.nf/customers.php")

        # Click the first "VIEW" button
        page.locator("a.btn-info").first.click()

        # Print the URL after the click and wait
        print(f"URL after click: {page.url}")
        time.sleep(5)

        # Take a screenshot of the customer details page
        page.screenshot(path="jules-scratch/verification/verification.png")

    finally:
        browser.close()

with sync_playwright() as playwright:
    run(playwright)