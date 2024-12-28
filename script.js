document.addEventListener('DOMContentLoaded', async () => {
    const form = document.getElementById('mobileForm');
    const messageDiv = document.getElementById('message');
    const formContainer = document.getElementById('form-container');

    // Fetch the user's IP address
    let userIP = '';
    try {
        const ipResponse = await fetch('https://api.ipify.org?format=json');
        const ipData = await ipResponse.json();
        userIP = ipData.ip;
    } catch (error) {
        console.error('Failed to fetch IP:', error);
    }

    // Check if the user has already submitted the number
    const isSubmitted = localStorage.getItem(`submitted-${userIP}`);
    if (isSubmitted) {
        formContainer.style.display = 'none';
        messageDiv.innerText = 'You have already submitted your mobile number.';
        return;
    }

    // Handle form submission
    form.addEventListener('submit', async (e) => {
        e.preventDefault();
        const mobileNumber = document.getElementById('mobile').value;

        try {
            const response = await fetch('https://yourdomain/apidata.php', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                },
                body: JSON.stringify({
                    mobile: mobileNumber,
                    ip: userIP,
                }),
            });

            if (response.ok) {
                localStorage.setItem(`submitted-${userIP}`, true);
                formContainer.style.display = 'none';
                messageDiv.innerText = 'Thank you! Your mobile number has been submitted.';
            } else {
                const errorData = await response.json();
                messageDiv.innerText = `Error: ${errorData.message || 'Submission failed.'}`;
            }
        } catch (error) {
            console.error('Error submitting the number:', error);
            messageDiv.innerText = 'An error occurred while submitting your number. Please try again.';
        }
    });
});
