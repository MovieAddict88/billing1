from playwright.sync_api import sync_playwright, expect

def run(playwright):
    browser = playwright.chromium.launch(headless=True)
    context = browser.new_context()
    page = context.new_page()

    # Log in
    page.goto("http://localhost:8000/login.php")
    page.fill("input[name='username']", "testadmin")
    page.fill("input[name='password']", "password")
    page.click("button[type='submit']")

    # Go to the bills page
    page.goto("http://localhost:8000/bills.php")

    # Click the "Generate Now" button
    page.get_by_role("link", name="Generate Now").click()

    # Wait for navigation and verify the URL
    expect(page).to_have_url("http://localhost:8000/bill_generation.php")

    # Take a screenshot
    page.screenshot(path="jules-scratch/verification/verification.png")

    browser.close()

with sync_playwright() as playwright:
    run(playwright)