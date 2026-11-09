"""Captura telas locais para o relatório: TEST_ADMIN_EMAIL e TEST_ADMIN_PASSWORD."""
import os
from pathlib import Path
from playwright.sync_api import sync_playwright

base = os.environ.get("SITE_URL", "http://localhost/loja_games/")
email = os.environ["TEST_ADMIN_EMAIL"]
senha = os.environ["TEST_ADMIN_PASSWORD"]
destino = Path(__file__).resolve().parents[1] / "docs" / "prints"
destino.mkdir(parents=True, exist_ok=True)

with sync_playwright() as playwright:
    browser = playwright.chromium.launch(
        headless=True,
        executable_path=r"C:\Program Files\Google\Chrome\Application\chrome.exe",
    )
    page = browser.new_page(viewport={"width": 1440, "height": 900}, device_scale_factor=1)
    page.goto(base, wait_until="networkidle")
    page.screenshot(path=str(destino / "catalogo.png"), full_page=True)
    page.locator(".game-card").first.click()
    page.screenshot(path=str(destino / "jogo.png"), full_page=True)
    page.goto(base + "cadastro.php", wait_until="networkidle")
    page.screenshot(path=str(destino / "cadastro.png"), full_page=True)
    page.goto(base + "login.php", wait_until="networkidle")
    page.fill("#email", email)
    page.fill("#senha-login", senha)
    page.get_by_role("button", name="Entrar").click()
    page.wait_for_url("**/admin/views/ver_games.php", timeout=10000)
    page.goto(base + "admin/views/ver_games.php", wait_until="networkidle")
    if "login.php" in page.url:
        raise RuntimeError("A conta de teste não acessou a área administrativa.")
    page.screenshot(path=str(destino / "administracao.png"), full_page=True)
    page.goto(base + "admin/views/cadastrar_game.php", wait_until="networkidle")
    assert page.locator("#categoria option").count() > 1
    assert page.locator("#plataforma option").count() > 1
    page.goto(base + "admin/views/ver_usuarios.php", wait_until="networkidle")
    page.get_by_role("heading", name="Usuários").wait_for()
    page.goto(base + "admin/views/gerenciar_taxonomia.php?tipo=plataformas", wait_until="networkidle")
    page.get_by_role("heading", name="Plataformas").wait_for()
    page.goto(base + "admin/views/gerenciar_taxonomia.php?tipo=categorias", wait_until="networkidle")
    page.get_by_role("heading", name="Categorias").wait_for()
    page.screenshot(path=str(destino / "categorias.png"), full_page=True)
    page.goto(base + "admin/views/gerenciar_avaliacoes.php", wait_until="networkidle")
    page.get_by_role("heading", name="Avaliações").wait_for()
    page.screenshot(path=str(destino / "avaliacoes.png"), full_page=True)
    page.goto(base + "carteira.php", wait_until="networkidle")
    page.screenshot(path=str(destino / "carteira.png"), full_page=True)
    browser.close()

print("Capturas criadas em", destino)
