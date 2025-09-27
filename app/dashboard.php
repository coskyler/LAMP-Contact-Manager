<?php
require_once __DIR__ . '/cookie.php';

$userId = checkAuthCookie();
if ($userId === null) {
    header('Location: /login.html');
    exit;
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Contact Manager</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
    <nav class="navbar navbar-expand-lg navbar-dark bg-dark">
        <div class="container-fluid">
            <a class="navbar-brand" href="#">Contact Manager</a>
            <div class="d-flex">
                <button id="logoutButton" class="btn btn-outline-light">Logout</button>
            </div>
        </div>
    </nav>

    <div class="container mt-4">
        <div class="row">
            <div class="col-md-4">
                <h2>Add New Contact</h2>
                <form id="addContactForm">
                    <div class="mb-3">
                        <label for="firstName" class="form-label">First Name</label>
                        <input type="text" class="form-control" id="firstName" required>
                    </div>
                    <div class="mb-3">
                        <label for="lastName" class="form-label">Last Name</label>
                        <input type="text" class="form-control" id="lastName" required>
                    </div>
                    <div class="mb-3">
                        <label for="email" class="form-label">Email</label>
                        <input type="email" class="form-control" id="email">
                    </div>
                    <div class="mb-3">
                        <label for="phone" class="form-label">Phone</label>
                        <input type="tel" class="form-control" id="phone">
                    </div>
                    <button type="submit" class="btn btn-primary">Add Contact</button>
                </form>
            </div>
            <div class="col-md-8">
                <h2>My Contacts</h2>
                <ul id="contactList" class="list-group">
                </ul>
            </div>
        </div>
    </div>

    <div class="modal fade" id="editContactModal" tabindex="-1" aria-labelledby="editContactModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="editContactModalLabel">Edit Contact</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <form id="editContactForm">
                        <input type="hidden" id="editContactId">
                        <div class="mb-3">
                            <label for="editFirstName" class="form-label">First Name</label>
                            <input type="text" class="form-control" id="editFirstName" required>
                        </div>
                        <div class="mb-3">
                            <label for="editLastName" class="form-label">Last Name</label>
                            <input type="text" class="form-control" id="editLastName" required>
                        </div>
                        <div class="mb-3">
                            <label for="editEmail" class="form-label">Email</label>
                            <input type="email" class="form-control" id="editEmail">
                        </div>
                        <div class="mb-3">
                            <label for="editPhone" class="form-label">Phone</label>
                            <input type="tel" class="form-control" id="editPhone">
                        </div>
                        <button type="submit" class="btn btn-primary">Save Changes</button>
                    </form>
                </div>
            </div>
        </div>
    </div>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const editContactModal = new bootstrap.Modal(document.getElementById('editContactModal'));
            fetchContacts();

            document.getElementById('logoutButton').addEventListener('click', async () => {
                const response = await fetch('/api/auth/logout', { method: 'POST' });
                if (response.ok) {
                    window.location.href = 'login.html';
                }
            });

            document.getElementById('addContactForm').addEventListener('submit', async function(event) {
                event.preventDefault();
                const firstName = document.getElementById('firstName').value;
                const lastName = document.getElementById('lastName').value;
                const email = document.getElementById('email').value;
                const phone = document.getElementById('phone').value;

                const response = await fetch('/api/contacts.php', {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/json' },
                    body: JSON.stringify({ first_name: firstName, last_name: lastName, email: email, phone_number: phone })
                });

                if (response.ok) {
                    fetchContacts();
                    this.reset();
                } else {
                    const result = await response.json();
                    alert(result.message || 'Failed to add contact');
                }
            });

            document.getElementById('contactList').addEventListener('click', async function(event) {
                const target = event.target;
                const contactId = target.dataset.id;

                if (target.classList.contains('btn-danger')) {
                    if (confirm('Are you sure you want to delete this contact?')) {
                        const response = await fetch('/api/contacts.php', {
                            method: 'DELETE',
                            headers: { 'Content-Type': 'application/json' },
                            body: JSON.stringify({ contact_id: contactId })
                        });
                        if (response.ok) {
                            fetchContacts();
                        } else {
                            const result = await response.json();
                            alert(result.message || 'Failed to delete contact');
                        }
                    }
                }

                if (target.classList.contains('btn-secondary')) {
                    document.getElementById('editContactId').value = contactId;
                    document.getElementById('editFirstName').value = target.dataset.firstName;
                    document.getElementById('editLastName').value = target.dataset.lastName;
                    document.getElementById('editEmail').value = target.dataset.email;
                    document.getElementById('editPhone').value = target.dataset.phone;
                    editContactModal.show();
                }
            });

            document.getElementById('editContactForm').addEventListener('submit', async function(event) {
                event.preventDefault();
                const contactId = document.getElementById('editContactId').value;
                const firstName = document.getElementById('editFirstName').value;
                const lastName = document.getElementById('editLastName').value;
                const email = document.getElementById('editEmail').value;
                const phone = document.getElementById('editPhone').value;

                const response = await fetch('/api/contacts.php', {
                    method: 'PUT',
                    headers: { 'Content-Type': 'application/json' },
                    body: JSON.stringify({
                        contact_id: contactId,
                        first_name: firstName,
                        last_name: lastName,
                        email: email,
                        phone_number: phone
                    })
                });

                if (response.ok) {
                    editContactModal.hide();
                    fetchContacts();
                } else {
                    const result = await response.json();
                    alert(result.message || 'Failed to update contact');
                }
            });
        });

        async function fetchContacts() {
            const response = await fetch('/api/contacts.php');
            const result = await response.json();
            const contacts = result.rows;
            const contactList = document.getElementById('contactList');
            contactList.innerHTML = '';

            if (!contacts || contacts.length === 0) {
                contactList.innerHTML = '<li class="list-group-item">No contacts found.</li>';
                return;
            }

            contacts.forEach(contact => {
                const li = document.createElement('li');
                li.className = 'list-group-item d-flex justify-content-between align-items-center';
                const phone = contact.phone_number || '';
                const email = contact.email || '';
                li.innerHTML = `
                    <div>
                        <strong>${contact.first_name} ${contact.last_name}</strong><br>
                        <small class="text-muted">${email} | ${phone}</small>
                    </div>
                    <div>
                        <button class="btn btn-sm btn-secondary" data-id="${contact.contact_id}" data-first-name="${contact.first_name}" data-last-name="${contact.last_name}" data-email="${email}" data-phone="${phone}">Edit</button>
                        <button class="btn btn-sm btn-danger" data-id="${contact.contact_id}">Delete</button>
                    </div>
                `;
                contactList.appendChild(li);
            });
        }
    </script>
</body>
</html>
