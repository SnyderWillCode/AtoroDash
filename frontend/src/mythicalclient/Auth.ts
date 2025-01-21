/* ---------------------------
 * Author: NaysKutzu Date: 2025-11-29
 *
 * Changes:
 * - Initial commit
 * - Added support for billing update
 *
 * ---------------------------*/
/* ---------------------------
 * Author: NaysKutzu Date: 2025-12-01
 *
 * Changes:
 * - Added support for changing the user info!
 *
 * ---------------------------*/

/* ---------------------------
 * Author: NaysKutzu Date: 2025-12-07
 *
 * Changes:
 * - Add support for 2fa!
 *
 *
 * ---------------------------*/
class Auth {
    /**
     * Logs the user in
     *
     * @param email The email to log in with
     * @param turnstileResponse The turnstile response
     *
     * @returns The response from the server
     */
    static async forgotPassword(email: string, turnstileResponse: string) {
        const response = await fetch('/api/user/auth/forgot', {
            method: 'POST',
            body: new URLSearchParams({
                email: email,
                turnstileResponse: turnstileResponse,
            }),
        });
        const data = await response.json();
        return data;
    }

    /**
     * Resets the password
     *
     * @param confirmPassword The password to confirm
     * @param password The new password
     * @param resetCode The reset code
     * @param turnstileResponse The turnstile response
     *
     * @returns The response from the server
     */
    static async resetPassword(
        confirmPassword: string,
        password: string,
        resetCode: string,
        turnstileResponse: string,
    ) {
        const response = await fetch('/api/user/auth/reset', {
            method: 'POST',
            body: new URLSearchParams({
                password: password,
                confirmPassword: confirmPassword,
                email_code: resetCode || '',
                turnstileResponse: turnstileResponse,
            }),
        });
        const data = await response.json();
        return data;
    }

    /**
     * Verifies the login token
     *
     * @param code The code to verify
     *
     * @returns The response from the server
     */
    static async isLoginVerifyTokenValid(code: string) {
        const response = await fetch(`/api/user/auth/reset?code=${code}`, {
            method: 'GET',
        });
        const data = await response.json();
        return data;
    }

    /**
     * Registers the user
     *
     * @param firstName The first name
     * @param lastName The last name
     * @param email The email
     * @param username The username
     * @param password The password
     * @param turnstileResponse The turnstile response
     *
     * @returns The response from the server
     */
    static async register(
        firstName: string,
        lastName: string,
        email: string,
        username: string,
        password: string,
        turnstileResponse: string,
    ) {
        const response = await fetch('/api/user/auth/register', {
            method: 'POST',
            body: new URLSearchParams({
                firstName: firstName,
                lastName: lastName,
                email: email,
                username: username,
                password: password,
                turnstileResponse: turnstileResponse,
            }),
        });
        const data = await response.json();
        return data;
    }
    /**
     * Logs the user in
     *
     * @param login The users email or username
     * @param password The users password
     * @param turnstileResponse The turnstile response
     *
     * @returns
     */
    static async login(login: string, password: string, turnstileResponse: string) {
        const response = await fetch('/api/user/auth/login', {
            method: 'POST',
            body: new URLSearchParams({
                login: login,
                password: password,
                turnstileResponse: turnstileResponse,
            }),
        });
        const data = await response.json();
        return data;
    }

    /**
     * Update the users billing information
     *
     * @param company_name The company name
     * @param vat_number The vat number
     * @param address1 The address line 1
     * @param address2 The address line 2
     * @param city The city
     * @param country The country
     * @param state The state
     * @param postcode The postcode
     *
     * @returns
     */
    static async updateBilling(
        company_name: string,
        vat_number: string,
        address1: string,
        address2: string,
        city: string,
        country: string,
        state: string,
        postcode: string,
    ) {
        const response = await fetch('/api/user/session/billing/update', {
            method: 'POST',
            body: new URLSearchParams({
                company_name: company_name,
                vat_number: vat_number,
                address1: address1,
                address2: address2,
                city: city,
                country: country,
                state: state,
                postcode: postcode,
            }),
        });
        const data = await response.json();
        return data;
    }

    /**
     * Update the users info
     *
     * @param first_name The first name
     * @param last_name The last name
     * @param email The email
     * @param avatar The avatar
     * @param background  The background
     *
     * @returns
     */
    static async updateUserInfo(
        first_name: string,
        last_name: string,
        email: string,
        avatar: string,
        background: string,
    ) {
        const response = await fetch('/api/user/session/info/update', {
            method: 'POST',
            body: new URLSearchParams({
                first_name: first_name,
                last_name: last_name,
                email: email,
                avatar: avatar,
                background: background,
            }),
        });
        const data = await response.json();
        return data;
    }
    /**
     * Setup 2fa
     *
     * @returns
     */
    static async getTwoFactorSecret() {
        const response = await fetch('/api/user/auth/2fa/setup', {
            method: 'GET',
        });
        const data = await response.json();
        return data;
    }

    /**
     * Verify 2fa
     *
     * @param code The code
     *
     * @returns
     */
    static async verifyTwoFactor(code: string, turnstileResponse: string) {
        const response = await fetch('/api/user/auth/2fa/setup', {
            method: 'POST',
            body: new URLSearchParams({
                code: code,
                turnstileResponse: turnstileResponse,
            }),
        });
        const data = await response.json();
        return data;
    }

    /**
     *
     * Reset the support pin!
     *
     * @returns The new pin
     */
    static async resetPin(): Promise<number> {
        const response = await fetch('/api/user/session/newPin', {
            method: 'POST',
        });
        const data = await response.json();
        return parseInt(data.pin, 10);
    }
}

export default Auth;
