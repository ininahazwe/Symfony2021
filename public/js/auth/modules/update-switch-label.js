/**
 * updates the switch and it's label depending on the server response
 * @param {boolean} is_guard_checking_IP Whether or not to verify the IP address during the authentification
 */
export default function updateSwitchAndItsLabel(is_guard_checking_IP){
    document.body.querySelector('label[for="check_user_ip_checkbox"]').textContent = is_guard_checking_IP ? "Active" : "Inactive";
    document.body.querySelector('input[id="check_user_ip_checkbox"]').checked = is_guard_checking_IP;
}