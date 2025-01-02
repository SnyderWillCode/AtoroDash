class Mails {
    /**
     * Get the Mails for the current session
     *
     * @returns The response from the server
     */
    public static async get() {
        const response = await fetch('/api/user/session/emails', {
            method: 'GET',
        });
        const data = await response.json();
        return data.emails;
    }
}

export default Mails;
