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
        try {
            const response = await fetch(`/api/user/invoice/${id}/pay`, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                },
            });
            const data = await response.json();
            return data;
        } catch (error) {
            console.error('Error paying invoice:', error);
            throw error;
        }
    }
}
export default OrderInvoices;
