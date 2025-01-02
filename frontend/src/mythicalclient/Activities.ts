class Activities {
    /**
     * Get the activities for the current session
     *
     * @returns The response from the server
     */
    public static async get() {
        const response = await fetch('/api/user/session/activities', {
            method: 'GET',
        });
        const data = await response.json();
        return data.activities;
    }
}

export default Activities;
