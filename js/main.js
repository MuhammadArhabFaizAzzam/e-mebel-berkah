// Berkah Mebel Ayu - Main JavaScript

// Navbar scroll effect - navbar tetap di atas tapi ikut scroll dengan animasi
const navbar = document.querySelector('nav');

window.addEventListener('scroll', () => {
    const scrollTop = window.scrollY;
    
    if (scrollTop > 50) {
        // Saat scroll - navbar lebih solid
        navbar.classList.add('shadow-xl');
        navbar.style.backgroundColor = 'rgba(45, 27, 20, 0.98)';
        navbar.style.backdropFilter = 'blur(12px)';
    } else {
        // Di atas - navbar lebih transparan
        navbar.classList.remove('shadow-xl');
        navbar.style.backgroundColor = 'rgba(45, 27, 20, 0.85)';
        navbar.style.backdropFilter = 'blur(10px)';
    }
});

// Shopping Cart Functionality
const cart = JSON.parse(localStorage.getItem('cart')) || [];

function addToCart(productName, price) {
    const existingItem = cart.find(item => item.name === productName);
    
    if (existingItem) {
        existingItem.quantity++;
    } else {
        cart.push({
            name: productName,
            price: parseFloat(price),
            quantity: 1
        });
    }
    
    localStorage.setItem('cart', JSON.stringify(cart));
    
    // Show notification
    showNotification(`${productName} ditambahkan ke keranjang!`);
}

function showNotification(message) {
    const notification = document.createElement('div');
    notification.className = 'fixed bottom-4 right-4 bg-green-500 text-white px-6 py-3 rounded-xl shadow-lg z-50 animate-fade-in-up';
    notification.textContent = message;
    document.body.appendChild(notification);
    
    setTimeout(() => {
        notification.remove();
    }, 3000);
}

// Add click handlers to all cart buttons
document.querySelectorAll('.add-to-cart').forEach(button => {
    button.addEventListener('click', function(e) {
        e.preventDefault();
        const productName = this.dataset.productName;
        const price = this.dataset.price;
        addToCart(productName, price);
    });
});

// Mobile menu toggle
const mobileMenuBtn = document.getElementById('mobile-menu-btn');
const mobileMenu = document.getElementById('mobile-menu');

if (mobileMenuBtn && mobileMenu) {
    mobileMenuBtn.addEventListener('click', () => {
        mobileMenu.classList.toggle('hidden');
    });

    // Close menu when clicking on a link
    mobileMenu.querySelectorAll('a').forEach(link => {
        link.addEventListener('click', () => {
            mobileMenu.classList.add('hidden');
        });
    });
}

// Stagger animation on scroll
const observerOptions = {
    threshold: 0.1,
    rootMargin: '0px 0px -50px 0px'
};

const observer = new IntersectionObserver((entries) => {
    entries.forEach(entry => {
        if (entry.isIntersecting) {
            entry.target.classList.add('stagger-animation');
            observer.unobserve(entry.target);
        }
    });
}, observerOptions);

// Observe elements
document.querySelectorAll('.wood-card, .testimonial-card').forEach(el => {
    observer.observe(el);
});

// Smooth scroll for anchor links
document.querySelectorAll('a[href^="#"]').forEach(anchor => {
    anchor.addEventListener('click', function (e) {
        e.preventDefault();
        const target = document.querySelector(this.getAttribute('href'));
        if (target) {
            target.scrollIntoView({
                behavior: 'smooth',
                block: 'start'
            });
        }
    });
});

// Wood texture parallax effect
window.addEventListener('scroll', () => {
    const scrolled = window.pageYOffset;
    const woodElements = document.querySelectorAll('.floating-wood');
    woodElements.forEach((el, index) => {
        const speed = 0.1 + (index * 0.05);
        const yPos = -(scrolled * speed);
        el.style.transform = `translateY(${yPos}px) rotate(${el.classList.contains('-rotate-12') ? '-12deg' : '12deg'})`;
    });
});
