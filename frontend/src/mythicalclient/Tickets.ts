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
}

export default Tickets;
