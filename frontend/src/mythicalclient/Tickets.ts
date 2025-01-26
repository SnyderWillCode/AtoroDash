class Tickets {
    public static async getTicketCreateInfo() {
        const response = await fetch('/api/user/ticket/create', {
            method: 'GET',
        });
        const data = await response.json();
        return data;
    }
    static async createTicket(
        department_id: number,
        subject: string,
        message: string,
        priority: string,
        service: number,
    ) {
        const response = await fetch('/api/user/ticket/create', {
            method: 'POST',
            body: new URLSearchParams({
                department_id: department_id.toString(),
                subject: subject,
                message: message,
                priority: priority,
                service: service.toString() || '',
            }),
        });
        const data = await response.json();
        return data;
    }
    static async getTickets() {
        const response = await fetch('/api/user/ticket/list', {
            method: 'GET',
        });
        const data = await response.json();
        return data;
    }

    static async getTicket(ticketId: number) {
        const response = await fetch(`/api/user/ticket/${ticketId}/messages`, {
            method: 'GET',
        });
        const data = await response.json();
        return data;
    }

    static async replyToTicket(ticketId: number, StrMessage: string) {
        const response = await fetch(`/api/user/ticket/${ticketId}/reply`, {
            method: 'POST',
            body: new URLSearchParams({
                message: StrMessage,
            }),
        });
        const data = await response.json();
        return data;
    }

    static async updateTicketStatus(ticketId: number, status: 'open' | 'closed' | 'waiting' | 'replied' | 'inprogress') {
        const response = await fetch(`/api/user/ticket/${ticketId}/status`, {
            method: 'POST',
            body: new URLSearchParams({
                status: status,
            }),
        });
        const data = await response.json();
        return data;
    }

    static async uploadAttachment(ticketId: number, file: File) {
        const formData = new FormData();
        formData.append('attachments', file);

        const response = await fetch(`/api/user/ticket/${ticketId}/attachments`, {
            method: 'POST',
            body: formData
        });
        const data = await response.json();
        return data;
    }
}

export default Tickets;
