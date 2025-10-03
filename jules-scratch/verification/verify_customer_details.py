from playwright.sync_api import sync_playwright

def run(playwright):
    browser = playwright.chromium.launch()
    context = browser.new_context()
    page = context.new_page()

    try:
        # Log in as admin and capture the debug output
        page.goto("http://localhost:8000/login.php")
        page.fill("input[name='username']", "admin")
        page.fill("input[name='password']", "password")
        # The form submission will go to approve.php, so we wait for navigation
        with page.expect_navigation():
            page.click("button[type='submit']")

        # Take a screenshot of the approve.php output
        page.screenshot(path="jules-scratch/verification/debug_output.png")

    finally:
        browser.close()

with sync_playwright() as playwright:
    run(playwright)