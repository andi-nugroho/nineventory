
document.addEventListener('alpine:init', () => {
    Alpine.store('cart', {
        items: JSON.parse(localStorage.getItem('nineventory_cart')) || [],

        init() {
            this.$watch('items', (val) => {
                localStorage.setItem('nineventory_cart', JSON.stringify(val));
            });
        },

        add(item, qty = 1, notes = '') {
            qty = parseInt(qty);
            const existing = this.items.find(i => i.id === item.id);
            if (existing) {
                // Ensure we don't exceed max stock
                const newQty = existing.qty + qty;
                if (newQty <= item.stok_tersedia) {
                    existing.qty = newQty;
                    // Optional: Append notes or overwrite? Let's overwrite if provided, or keep existing.
                    if (notes) existing.notes = notes;
                } else {
                    alert(`Maximum stock for ${item.nama_barang} is ${item.stok_tersedia}`);
                    existing.qty = item.stok_tersedia;
                }
            } else {
                this.items.push({
                    id: item.id,
                    name: item.nama_barang,
                    category: item.kategori,
                    maxStock: item.stok_tersedia,
                    qty: qty,
                    notes: notes
                });
            }
        },

        updateQty(id, newQty) {
            const item = this.items.find(i => i.id === id);
            if (item) {
                newQty = parseInt(newQty);
                if (newQty > 0) {
                    if (newQty <= item.maxStock) {
                        item.qty = newQty;
                    } else {
                        alert(`Max stock is ${item.maxStock}`);
                        item.qty = item.maxStock;
                    }
                }
            }
        },

        remove(id) {
            this.items = this.items.filter(i => i.id !== id);
        },

        clear() {
            this.items = [];
            localStorage.removeItem('nineventory_cart');
        },

        get count() {
            return this.items.reduce((acc, item) => acc + item.qty, 0);
        },

        get hasItems() {
            return this.items.length > 0;
        }
    });
});
