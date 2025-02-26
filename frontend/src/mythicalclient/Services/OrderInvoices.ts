class OrderInvoices {
    public static async getUserInvoices() {
        const response = await fetch(`/api/user/invoices`);
        const data = await response.json();
        return data.invoices;
    }

    public static async getUserInvoice(id: string) {
        const response = await fetch(`/api/user/invoice/${id}`);
        const data = await response.json();
        return data.invoice;
    }

    public static async payUserInvoice(id: string) {
        const response = await fetch(`/api/user/invoice/${id}/pay`, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
            },
        });
        const data = await response.json();
        return data;
    }
}
export default OrderInvoices;
