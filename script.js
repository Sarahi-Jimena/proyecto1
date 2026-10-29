let cart = [];

const money = (n) => '$' + Number(n).toFixed(2);
const esc = (s) => String(s).replace(/[&<>"']/g, (c) => ({ '&': '&amp;', '<': '&lt;', '>': '&gt;', '"': '&quot;', "'": '&#39;' }[c]));

function addToCart(product) {
    const existing = cart.find((item) => item.id === product.id);
    if (existing) {
        if (existing.qty + 1 > product.cantidad) {
            alert(`Stock insuficiente: solo quedan ${product.cantidad} unidades.`);
            return;
        }
        existing.qty++;
    } else {
        if (product.cantidad < 1) {
            alert('Producto agotado.');
            return;
        }
        cart.push({ ...product, qty: 1 });
    }
    renderCart();
}

function changeQty(id, delta) {
    const item = cart.find((i) => i.id === id);
    if (!item) return;
    const nueva = item.qty + delta;
    if (nueva > item.cantidad) {
        alert(`Stock insuficiente: solo quedan ${item.cantidad} unidades.`);
        return;
    }
    if (nueva < 1) {
        cart = cart.filter((i) => i.id !== id);
    } else {
        item.qty = nueva;
    }
    renderCart();
}

function renderCart() {
    const container = document.getElementById('cart-items');
    const totalEl = document.getElementById('cart-total-amount');
    if (!container || !totalEl) return;

    if (cart.length === 0) {
        container.innerHTML = '<p class="cart-empty">Aún no hay productos en la orden.</p>';
        totalEl.textContent = '$0.00';
        return;
    }

    let total = 0;
    container.innerHTML = cart.map((item) => {
        const subtotal = item.precio * item.qty;
        total += subtotal;
        return `
            <div class="cart-item">
                <div>
                    <strong>${esc(item.producto)}</strong><br>
                    <small>${money(item.precio)} c/u</small>
                    <div class="qty-controls">
                        <button type="button" aria-label="Quitar uno" onclick="changeQty(${item.id}, -1)">−</button>
                        <span>${item.qty}</span>
                        <button type="button" aria-label="Agregar uno" onclick="changeQty(${item.id}, 1)">+</button>
                    </div>
                </div>
                <strong>${money(subtotal)}</strong>
            </div>`;
    }).join('');

    totalEl.textContent = money(total);
}

function printTicket(idVenta, items, total) {
    const fecha = new Date().toLocaleString('es-MX');
    const filas = items.map((i) =>
        `<tr><td>${i.qty} x ${esc(i.producto)}</td><td style="text-align:right">${money(i.precio * i.qty)}</td></tr>`
    ).join('');

    const w = window.open('', '_blank', 'width=360,height=560');
    if (!w) return; // el navegador bloqueó la ventana emergente
    w.document.write(`<!DOCTYPE html><html lang="es"><head><meta charset="UTF-8"><title>Ticket #${idVenta}</title>
        <style>body{font:13px monospace;width:300px;margin:12px auto}h2,p{text-align:center;margin:4px 0}
        table{width:100%;border-collapse:collapse;margin:10px 0}td{padding:2px 0}
        .t{border-top:1px dashed #000;font-weight:bold;font-size:15px}</style></head><body>
        <h2>Tienda Sara</h2><p>Ticket #${idVenta}</p><p>${fecha}</p>
        <table>${filas}<tr class="t"><td>TOTAL</td><td style="text-align:right">${money(total)}</td></tr></table>
        <p>¡Gracias por su compra!</p></body></html>`);
    w.document.close();
    w.focus();
    w.print();
}

async function processSale() {
    if (cart.length === 0) {
        alert('Agrega al menos un producto para cobrar.');
        return;
    }

    const btn = document.getElementById('btn-checkout');
    btn.disabled = true;

    const items = cart.map((i) => ({ id: i.id, qty: i.qty }));

    try {
        const response = await fetch('procesar_venta.php', {
            method: 'POST',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify({ items })
        });
        const result = await response.json();

        if (result.success) {
            printTicket(result.id_venta, cart, result.total);
            cart = [];
            renderCart();
            setTimeout(() => window.location.reload(), 800);
        } else {
            alert('No se pudo registrar la venta: ' + result.message);
            btn.disabled = false;
        }
    } catch (error) {
        alert('No se pudo conectar con el servidor.');
        console.error(error);
        btn.disabled = false;
    }
}

document.getElementById('btn-checkout')?.addEventListener('click', processSale);

document.getElementById('products-container')?.addEventListener('click', (e) => {
    const btn = e.target.closest('.btn-add');
    if (btn && !btn.disabled) addToCart(JSON.parse(btn.dataset.producto));
});

function applyFilters() {
    const term = (document.getElementById('search-box')?.value || '').toLowerCase().trim();
    const cat = document.querySelector('.cat-chip.active')?.dataset.cat || 'all';
    document.querySelectorAll('.product-card').forEach((card) => {
        const okName = card.dataset.name.includes(term);
        const okCat = cat === 'all' || card.dataset.category === cat;
        card.style.display = okName && okCat ? 'flex' : 'none';
    });
}

document.getElementById('search-box')?.addEventListener('input', applyFilters);

document.querySelectorAll('.cat-chip').forEach((chip) => {
    chip.addEventListener('click', () => {
        document.querySelectorAll('.cat-chip').forEach((c) => c.classList.remove('active'));
        chip.classList.add('active');
        applyFilters();
    });
});
