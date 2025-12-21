function viewPassword(event, id) {
    const password = document.querySelector(id)
    const text = event.target.closest('.x-view-password')
    text.querySelector('i').classList.remove('fa-eye')
    text.querySelector('i').classList.remove('fa-eye-slash')

    if (password.type == 'text') {
        password.type = 'password'
        text.querySelector('i').classList.add('fa-eye')
    } else {
        password.type = 'text'
        text.querySelector('i').classList.add('fa-eye-slash')
    }
}