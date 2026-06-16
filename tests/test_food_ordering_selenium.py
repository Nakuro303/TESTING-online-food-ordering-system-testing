import os
import time
import pytest
from selenium import webdriver
from selenium.webdriver.chrome.options import Options
from selenium.webdriver.common.by import By
from selenium.webdriver.support.ui import WebDriverWait, Select
from selenium.webdriver.support import expected_conditions as EC
from selenium.common.exceptions import TimeoutException, NoAlertPresentException

BASE_URL       = "http://localhost/online-food-ordering-system"
USER_EMAIL     = "testuser@gmail.com"
USER_PASSWORD  = "Test@1234"
USER_FNAME     = "Budi"
USER_LNAME     = "Santoso"
USER_CONTACT   = "08123456789"
ADMIN_EMAIL    = "admin@gmail.com"
ADMIN_PASSWORD = "admin2024"
NEW_ITEM_NAME  = "Es Teh Manis Selenium"
NEW_ITEM_DESC  = "Minuman segar otomatis dari Selenium"
NEW_ITEM_PRICE = "12000"
NEW_ITEM_IMG   = os.path.abspath("test_image.png")

@pytest.fixture
def driver():
    options = Options()
    options.add_argument("--start-maximized")
    options.add_experimental_option("excludeSwitches", ["enable-automation"])
    drv = webdriver.Chrome(options=options)
    drv.implicitly_wait(5)
    yield drv
    drv.quit()

def dismiss_all_alerts(driver, timeout=8):
    deadline = time.time() + timeout
    while time.time() < deadline:
        try:
            WebDriverWait(driver, 2).until(EC.alert_is_present())
            driver.switch_to.alert.accept()
            time.sleep(0.3)
        except (TimeoutException, NoAlertPresentException):
            break

def accept_confirm(driver, timeout=8):
    WebDriverWait(driver, timeout).until(EC.alert_is_present())
    driver.switch_to.alert.accept()

def js_click(driver, element):
    driver.execute_script("arguments[0].scrollIntoView({block: 'center'});", element)
    time.sleep(0.3)
    driver.execute_script("arguments[0].click();", element)

def login(driver, email=USER_EMAIL, password=USER_PASSWORD):
    driver.get(f"{BASE_URL}/login.php")
    wait = WebDriverWait(driver, 10)

    try:
        sign_in_panel = driver.find_element(By.ID, "sign-in-btn")
        if sign_in_panel.is_displayed():
            sign_in_panel.click()
            time.sleep(0.5)
    except Exception:
        pass

    wait.until(EC.visibility_of_element_located(
        (By.CSS_SELECTOR, ".sign-in-form input[name='email']")
    )).send_keys(email)
    driver.find_element(
        By.CSS_SELECTOR, ".sign-in-form input[name='password']"
    ).send_keys(password)
    driver.find_element(By.ID, "loginButton").click()
    dismiss_all_alerts(driver)

def _make_dummy_image(path: str):
    import struct, zlib

    def chunk(t, d):
        c = t + d
        return struct.pack(">I", len(d)) + c + struct.pack(">I", zlib.crc32(c) & 0xFFFFFFFF)

    data = (b"\x89PNG\r\n\x1a\n"
            + chunk(b"IHDR", struct.pack(">IIBBBBB", 1, 1, 8, 2, 0, 0, 0))
            + chunk(b"IDAT", zlib.compress(b"\x00\xff\xff\xff"))
            + chunk(b"IEND", b""))
    with open(path, "wb") as f:
        f.write(data)


#  SCENARIO 1
#  Sign Up → Login → Pilih Makanan → Add to Cart → Isi Info Pengguna → Place Order → Selesai
def test_sign_up_order_and_complete(driver):
    wait = WebDriverWait(driver, 10)

    driver.get(f"{BASE_URL}/login.php")
    wait.until(EC.element_to_be_clickable((By.ID, "sign-up-btn"))).click()
    time.sleep(1.0)

    wait.until(EC.visibility_of_element_located(
        (By.CSS_SELECTOR, ".sign-up-form input[name='firstName']")
    )).send_keys(USER_FNAME)
    driver.find_element(By.CSS_SELECTOR, ".sign-up-form input[name='lastName']").send_keys(USER_LNAME)
    driver.find_element(By.CSS_SELECTOR, ".sign-up-form input[name='email']").send_keys(USER_EMAIL)
    driver.find_element(By.CSS_SELECTOR, ".sign-up-form input[name='contact']").send_keys(USER_CONTACT)
    driver.find_element(By.CSS_SELECTOR, ".sign-up-form input[name='password']").send_keys(USER_PASSWORD)

    js_click(driver, driver.find_element(By.ID, "registerButton"))
    dismiss_all_alerts(driver)
    wait.until(EC.url_contains("/login.php"))

    login(driver)
    wait.until(EC.url_contains("/menu.php"))
    assert "login" not in driver.current_url
    add_btn = wait.until(EC.presence_of_element_located(
        (By.CSS_SELECTOR, "button.addItemBtn:not(.disabled-button)")
    ))
    js_click(driver, add_btn)
    time.sleep(1.5)

    driver.get(f"{BASE_URL}/cart.php")
    wait.until(EC.url_contains("/cart.php"))
    items = driver.find_elements(By.CSS_SELECTOR, ".cart-items .list-group-item")
    assert len(items) > 0, "Cart kosong setelah add to cart"

    driver.find_element(By.ID, "cash").click()
    driver.find_element(By.ID, "checkout-button").click()
    wait.until(EC.url_contains("/order_review.php"))
    wait.until(EC.visibility_of_element_located((By.ID, "firstName"))).clear()
    driver.find_element(By.ID, "firstName").send_keys(USER_FNAME)
    driver.find_element(By.ID, "lastName").send_keys(USER_LNAME)
    driver.find_element(By.ID, "contact").send_keys(USER_CONTACT)
    driver.find_element(By.ID, "address").send_keys("Jl. Merdeka No. 17, Jakarta")
    driver.find_element(By.ID, "order_note").send_keys("Tidak pedas ya kak")

    js_click(driver, driver.find_element(By.CSS_SELECTOR, "button.order-btn"))
    wait.until(EC.url_contains("/order_confirm.php"))
    assert "order_confirm" in driver.current_url, "Order tidak berhasil diproses"


#  SCENARIO 2
#  Login → My Orders → Cancel Order (isi alasan) → Logout

def test_cancel_order_with_reason(driver):
    wait = WebDriverWait(driver, 10)

    login(driver)
    wait.until(EC.url_contains("/menu.php"))

    driver.get(f"{BASE_URL}/orders.php")
    wait.until(EC.url_contains("/orders.php"))

    pending_tab = wait.until(EC.element_to_be_clickable(
        (By.XPATH, "//div[contains(@class,'tab') and normalize-space()='Pending']")
    ))
    pending_tab.click()

    cancel_btn = WebDriverWait(driver, 15).until(
        EC.presence_of_element_located((By.CSS_SELECTOR, "button.cancel-btn"))
    )
    js_click(driver, cancel_btn)

    reason_box = wait.until(EC.visibility_of_element_located((By.ID, "cancelReason")))
    reason_box.clear()
    reason_box.send_keys("Saya ingin mengubah pesanan saya")

    driver.find_element(By.ID, "cancelOrderBtn").click()
    dismiss_all_alerts(driver)
    wait.until(EC.url_contains("/orders.php"))

    cancelled_tab = wait.until(EC.element_to_be_clickable(
        (By.XPATH, "//div[contains(@class,'tab') and normalize-space()='Cancelled']")
    ))
    cancelled_tab.click()

    WebDriverWait(driver, 15).until(
        EC.presence_of_element_located((By.CSS_SELECTOR, "#cancelled-orders .order"))
    )
    assert driver.find_element(By.ID, "cancelled-orders").is_displayed()

    driver.get(f"{BASE_URL}/logout.php")
    wait.until(EC.url_contains("/login.php"))
    assert driver.find_element(By.ID, "loginButton").is_displayed()


#  SCENARIO 3
#  Login Admin → Menu Management → Tambah Item Baru → Simpan → Logout
def test_admin_add_menu_item(driver):
    wait = WebDriverWait(driver, 10)

    login(driver, email=ADMIN_EMAIL, password=ADMIN_PASSWORD)
    WebDriverWait(driver, 15).until(
        lambda d: "login" not in d.current_url.lower()
    )

    driver.get(f"{BASE_URL}/Admin/admin_menu.php")
    wait.until(EC.url_contains("admin_menu.php"))
    wait.until(EC.element_to_be_clickable(
        (By.XPATH, "//button[contains(.,'Add New Item')]")
    )).click()

    modal = wait.until(EC.visibility_of_element_located((By.ID, "itemModal")))
    modal.find_element(By.ID, "itemName").send_keys(NEW_ITEM_NAME)
    modal.find_element(By.ID, "description").send_keys(NEW_ITEM_DESC)

    Select(modal.find_element(By.ID, "status")).select_by_visible_text("Available")

    modal.find_element(By.ID, "price").send_keys(NEW_ITEM_PRICE)
    cat_select_el = modal.find_element(By.CSS_SELECTOR, "select[name='catName']")
    cat_select    = Select(cat_select_el)
    cat_selected  = cat_select.options[1].text
    cat_select.select_by_index(1)

    if not os.path.exists(NEW_ITEM_IMG):
        _make_dummy_image(NEW_ITEM_IMG)
    modal.find_element(By.ID, "image").send_keys(NEW_ITEM_IMG)

    print(f"\n  → Menambah item: '{NEW_ITEM_NAME}' | Kategori: {cat_selected} | Rp{NEW_ITEM_PRICE}")
    modal.find_element(By.CSS_SELECTOR, "button[type='submit']").click()
    dismiss_all_alerts(driver)
    wait.until(EC.url_contains("admin_menu.php"))

    assert NEW_ITEM_NAME in driver.page_source, \
        f"Item '{NEW_ITEM_NAME}' tidak ditemukan di tabel menu setelah disimpan"
    print(f"  → Item '{NEW_ITEM_NAME}' berhasil ditambahkan ✓")

    driver.get(f"{BASE_URL}/Admin/logout.php")
    wait.until(EC.url_contains("/login.php"))
    assert driver.find_element(By.ID, "loginButton").is_displayed()


#  SCENARIO 4
#  Login Admin → Menu Management → Hapus item yang ditambahkan di Scenario 3 → Konfirmasi → Verifikasi → Logout
def test_admin_delete_menu_item(driver):
    wait = WebDriverWait(driver, 10)

    login(driver, email=ADMIN_EMAIL, password=ADMIN_PASSWORD)
    WebDriverWait(driver, 15).until(
        lambda d: "login" not in d.current_url.lower()
    )

    driver.get(f"{BASE_URL}/Admin/admin_menu.php")
    wait.until(EC.url_contains("admin_menu.php"))
    assert NEW_ITEM_NAME in driver.page_source, (
        f"Item '{NEW_ITEM_NAME}' tidak ditemukan. "
        "Jalankan test_admin_add_menu_item terlebih dahulu."
    )

    delete_btn = wait.until(EC.element_to_be_clickable(
        (By.XPATH,
         f"//td[normalize-space(text())='{NEW_ITEM_NAME}']"
         f"/following-sibling::td//button[@id='deletebtn']")
    ))

    print(f"\n  → Menghapus item: '{NEW_ITEM_NAME}'")
    js_click(driver, delete_btn)
    accept_confirm(driver)
    wait.until(EC.url_contains("admin_menu.php"))
    time.sleep(1)   # tunggu halaman fully reload

    assert NEW_ITEM_NAME not in driver.page_source, \
        f"Item '{NEW_ITEM_NAME}' masih ada di tabel setelah dihapus"
    print(f"  → Item '{NEW_ITEM_NAME}' berhasil dihapus ✓")

    driver.get(f"{BASE_URL}/Admin/logout.php")
    wait.until(EC.url_contains("/login.php"))
    assert driver.find_element(By.ID, "loginButton").is_displayed()