function showReservationForm() {
    document.getElementById('loginForm').style.display = 'none';
    document.getElementById('reservationForm').style.display = 'block';
}
// Function to open the sign-up modal
function openSignupModal() {
    document.getElementById('signupModal').style.display = 'block';
}

// Function to close the sign-up modal
function closeSignupModal() {
    document.getElementById('signupModal').style.display = 'none';
}

// Function to submit the sign-up form 
function submitSignupForm() {

    //  close the modal
    closeSignupModal();
}

function createAccount() {
    var nameInput = document.getElementById('name');
    var phoneInput = document.getElementById('phone');

    // Validation
    if (nameInput.checkValidity() && phoneInput.checkValidity()) {
        // display an alert 
        alert('Account created successfully!');
    } else {
        // display an error message
        alert('Please enter valid information.');
    }
}
document.addEventListener('DOMContentLoaded', function() {
    const parkingSlots = document.querySelectorAll('.parking-slot');
    const selectedSlotInput = document.getElementById('selectedSlot');

    parkingSlots.forEach(slot => {
        slot.addEventListener('click', function() {
            if (this.classList.contains('reserved')) {
                this.classList.remove('reserved');
                this.classList.add('vacant');
                selectedSlotInput.value = ""; // Set to empty when unselecting
            } else {
                // Check if another slot is already selected
                const selectedSlots = document.querySelectorAll('.parking-slot.reserved');
                if (selectedSlots.length === 0) {
                    this.classList.remove('vacant');
                    this.classList.add('reserved');
                    selectedSlotInput.value = this.innerText; // Set to the selected slot
                } else {
                    alert('You can only reserve one slot at a time.');
                }
            }
        });
    });
});