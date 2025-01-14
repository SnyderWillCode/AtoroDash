class Announcements {
    /**
     * Fetch announcements from the API
     *
     * @returns The announcements data
     */
    static async fetchAnnouncements() {
        const response = await fetch('/api/user/announcements', {
            method: 'GET',
        });
        const data = await response.json();
        return data.announcements;
    }
}

export default Announcements;
