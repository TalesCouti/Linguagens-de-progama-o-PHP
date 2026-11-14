"""Confere cadastro, login, wishlist, avaliação e carteira no site local."""
import os
import subprocess
import uuid

from playwright.sync_api import sync_playwright


base = os.environ.get("SITE_URL", "http://localhost/loja_games/")
chrome = os.environ.get("CHROME_PATH", r"C:\Program Files\Google\Chrome\Application\chrome.exe")
php = os.environ.get("PHP_BIN", r"C:\xampp\php\php.exe")
email = "verificacao-" + uuid.uuid4().hex[:12] + "@example.com"
senha = "teste-academico-123"
os.environ["E2E_EMAIL"] = email

try:
    with sync_playwright() as playwright:
        browser = playwright.chromium.launch(headless=True, executable_path=chrome)
        page = browser.new_page(viewport={"width": 1280, "height": 900})
        try:
            page.goto(base + "cadastro.php", wait_until="networkidle")
            page.fill("#nome", "Conta de verificação")
            page.fill("#email", email)
            page.fill("#telefone", "11999999999")
            page.fill("#senha", senha)
            page.get_by_role("button", name="Criar conta").click()
            page.wait_for_url("**/login.php?cadastro=sucesso")

            page.fill("#email", email)
            page.fill("#senha-login", senha)
            page.get_by_role("button", name="Entrar").click()
            page.wait_for_url("**/index.php")
            assert "R$ 0,00" in page.locator(".nav-wallet").inner_text()
            jogo = page.locator(".game-card").first.get_attribute("href")
            assert jogo and jogo.startswith("game.php?id=")

            page.goto(base + jogo, wait_until="networkidle")
            page.get_by_role("button", name="Adicionar à wishlist").click()
            page.get_by_role("button", name="Remover da wishlist").wait_for()
            page.select_option("#nota", "5")
            page.fill("#comentario", "Comentário de verificação")
            page.get_by_role("button", name="Publicar avaliação").click()
            page.locator(".review-item p", has_text="Comentário de verificação").wait_for()
            page.get_by_role("button", name="Excluir minha avaliação").click()
            assert page.locator(".review-item p", has_text="Comentário de verificação").count() == 0
            page.get_by_role("button", name="Remover da wishlist").click()
            page.get_by_role("button", name="Adicionar à wishlist").wait_for()

            page.goto(base + "carteira.php", wait_until="networkidle")
            page.fill("#valor", "10.00")
            page.get_by_role("button", name="Adicionar saldo").click()
            page.wait_for_url("**/carteira.php?recarga=sucesso")
            assert "R$ 10,00" in page.locator(".nav-wallet").inner_text()
            page.goto(base + jogo, wait_until="networkidle")
            page.get_by_role("link", name="Adicionar saldo para comprar").wait_for()
            print("Cadastro, login, wishlist, avaliação e carteira: OK")
        finally:
            browser.close()
finally:
    subprocess.run(
        [php, "-r", 'require "admin/config/conexao.php"; $email=getenv("E2E_EMAIL"); if (str_starts_with($email, "verificacao-")) { $stmt=$pdo->prepare("DELETE FROM usuarios WHERE email=?"); $stmt->execute([$email]); }'],
        check=True,
    )
