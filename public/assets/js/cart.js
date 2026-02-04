
document.addEventListener('alpine:init', () => {
    Alpine.store('cart', {
        items: JSON.parse(localStorage.getItem('nineventory_cart')) || [],

        init() {
            this.$watch('items', (val) => {
                localStorage.setItem('nineventory_cart', JSON.stringify(val));
            });
        },

        add(item, jumlah = 1, notes = '') {
            jumlah = parseInt(jumlah);
            const existing = this.items.find(i => i.inventaris_id === item.id);
            if (existing) {
                // Ensure we don't exceed max stock
                const newJumlah = existing.jumlah + jumlah;
                if (newJumlah <= item.stok_tersedia) {
                    existing.jumlah = newJumlah;
                    // Optional: Append notes or overwrite? Let's overwrite if provided, or keep existing.
                    if (notes) existing.notes = notes;
                } else {
                    alert(`Maximum stock for ${item.nama_barang} is ${item.stok_tersedia}`);
                    existing.jumlah = item.stok_tersedia;
                }
            } else {
                this.items.push({
                    inventaris_id: item.id,
                    name: item.nama_barang,
                    category: item.kategori,
                    maxStock: item.stok_tersedia,
                    jumlah: jumlah,
                    notes: notes
                });
            }
        },

        updateQty(inventaris_id, newJumlah) {
            const item = this.items.find(i => i.inventaris_id === inventaris_id);
            if (item) {
                newJumlah = parseInt(newJumlah);
                if (newJumlah > 0) {
                    if (newJumlah <= item.maxStock) {
                        item.jumlah = newJumlah;
                    } else {
                        alert(`Max stock is ${item.maxStock}`);
                        item.jumlah = item.maxStock;
                    }
                }
            }
        },

        remove(inventaris_id) {
            this.items = this.items.filter(i => i.inventaris_id !== inventaris_id);
        },

        clear() {
            this.items = [];
            localStorage.removeItem('nineventory_cart');
        },

        get count() {
            return this.items.reduce((acc, item) => acc + item.jumlah, 0);
        },

        get hasItems() {
            return this.items.length > 0;
        }
    });
});
