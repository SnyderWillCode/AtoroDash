class Dashboard {
    /**
     * Get the dashboard data
     *
     * @returns
     */
    public static async get() {
        const response = await fetch('/api/admin', {
            method: 'GET',
        });
        return await response.json();
    }
}

export default Dashboard;
