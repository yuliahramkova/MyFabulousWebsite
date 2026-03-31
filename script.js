let cart = [];

const productsDB = [
    { id: 'dill', name: 'Укроп Гладиатор', price: 12, category: 'greens' },
    { id: 'tomat', name: 'Томат Бычье сердце', price: 34, category: 'vegetables' },
    { id: 'apple', name: 'Яблоня Слава победителям', price: 481, category: 'trees' }
];

// Сохранение корзины в LocalStorage
const saveCartToLocalStorage = () => {
    LocalStorage.setItem("cart", JSON.stringify(cart));
    console.log('Корзина сохранена:', cart); 
};
    
// Загрузка корзины из LocalStorage
const loadCartFromLocalStorage = () => {
    const savedCart = localStorage.getItem('cart');
    if (savedCart) {
        cart = JSON.parse(savedCart);
        console.log('Корзина загружена:', cart); // для отладки, потом можно удалить
        renderCart();
    }
};


// Добавление товара в корзину
const addToCart = (product) => {
    const existing = cart.find(item => item.id === product.id);
    if (existing) {
        existing.quantity += 1;
    } else {
        cart.push({ ...product, quantity: 1 }); /* со всем уже сущ свойствами product*/
    }
    saveCartToLocalStorage();
    renderCart();
};

// Удаление товара из корзины
const removeFromCart = (id) => {
    cart = cart.filter(item => item.id !== id);
    saveCartToLocalStorage();
    renderCart();
};

// Подсчёт общей суммы
const calculateTotal = () => 
    cart.reduce((total, item) => total + item.price * item.quantity, 0);

// Отрисовка корзины
const renderCart = () => {
    const cartList = document.querySelector('#cart-items');
    const totalEl = document.querySelector('#cart-total');
        
    // Очищаем список
    cartList.innerHTML = '';
    
    if (cart.length === 0) {
        cartList.innerHTML = '<li><em>Корзина пуста</em></li>';
    } else {
        cart.forEach(item => {
            const li = document.createElement('li');
            li.innerHTML = `
                <span>${item.name} × ${item.quantity}</span>
                <div>
                    <strong>${item.price * item.quantity} ₽</strong>
                    <button class="remove-item" data-id="${item.id}">✕</button>
                </div>
            `;
            cartList.appendChild(li);
        });
        
        // Вешаем обработчики на кнопки удаления
        document.querySelectorAll('.remove-item').forEach(btn => {
            btn.addEventListener('click', (e) => {
                const id = e.target.dataset.id;
                removeFromCart(id);
            });
        });
    }
    
    // Обновляем итог
    totalEl.textContent = calculateTotal();
};

// Фильтрация товаров
const filterProducts = (category) => {
    const products = document.querySelectorAll('.product-card');
    
    products.forEach(product => {
        const productCat = product.dataset.category;
        if (category === 'all' || productCat === category) {
            product.classList.remove('hidden');
        } else {
            product.classList.add('hidden');
        }
    });
};

// Обработка оплаты
const handlePayment = () => {
    if (cart.length === 0) {
        alert('Корзина пуста! Добавьте товары для оплаты.');
        return;
    }
    const total = calculateTotal();
    const confirmPay = confirm(`Оплатить заказ на сумму ${total} ₽?`); /* для диаголового окна*/
    if (confirmPay) {
        alert('Оплата прошла успешно! Спасибо за покупку =)');
        cart = [];
        saveCartToLocalStorage(); 
        renderCart();
    }
};

// 1. Кнопки "В корзину"
document.querySelectorAll('.add-to-cart').forEach(btn => {
    btn.addEventListener('click', (e) => {
        const product = {
            id: e.target.dataset.id,
            name: e.target.dataset.name,
            price: Number(e.target.dataset.price),
            category: e.target.dataset.category
        };
        addToCart(product);
        alert(`✅ ${product.name} добавлен в корзину!`);
    });
});

// 2. Фильтр категорий
const filterSelect = document.querySelector('#category-filter');
if (filterSelect) {
    filterSelect.addEventListener('change', (e) => {
        filterProducts(e.target.value);
    });
}

// 3. Очистка корзины
const clearBtn = document.querySelector('#clear-cart');
if (clearBtn) {
    clearBtn.addEventListener('click', () => {
        if (cart.length > 0 && confirm('Очистить корзину?')) {
            cart = [];
            saveCartToLocalStorage();
            renderCart();
        }
    });
}

// 4. Кнопка оплаты
const payBtn = document.querySelector('#pay-btn');
if (payBtn) {
    payBtn.addEventListener('click', handlePayment);
}

// Первоначальная отрисовка корзины
renderCart();