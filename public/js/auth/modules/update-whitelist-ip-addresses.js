/**
 * Updates the whitelist of IP addresses in the table and hides the modal.
 * @param {string|null} user_IP The IP address of the user.
 */
export default function updateWhitelistIpAddresses(user_IP){
    const user_ip_addresses = document.body.querySelector('p[id="user_ip_addresses"]');

    if (user_ip_addresses.textContent === ""){
        user_ip_addresses.textContent = user_IP;
        return;
    }

    if (!user_ip_addresses.textContent.includes(user_IP)){
        user_ip_addresses.textContent += ` | ${user_IP}`;
    }
}