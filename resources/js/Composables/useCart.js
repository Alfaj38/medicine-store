import { ref, computed } from 'vue';

const cart = ref({});

export function useCart() {
    const addToCart = (medicine) => {
        if (cart.value[medicine.id]) {
            cart.value[medicine.id].quantity++;
        } else {
            cart.value[medicine.id] = { ...medicine, quantity: 1 };
        }
    };

    const removeFromCart = (medicineId) => {
        delete cart.value[medicineId];
    };

    const updateQuantity = (medicineId, change) => {
        if (cart.value[medicineId]) {
            cart.value[medicineId].quantity += change;
            if (cart.value[medicineId].quantity <= 0) {
                removeFromCart(medicineId);
            }
        }
    };

    const cartTotal = computed(() => {
        return Object.values(cart.value).reduce((total, item) => total + ((parseFloat(item.sell_price) || 0) * item.quantity), 0);
    });

    const cartCount = computed(() => {
        return Object.values(cart.value).reduce((count, item) => count + item.quantity, 0);
    });

    const clearCart = () => {
        cart.value = {};
    };

    return {
        cart,
        addToCart,
        removeFromCart,
        updateQuantity,
        cartTotal,
        cartCount,
        clearCart
    };
}
