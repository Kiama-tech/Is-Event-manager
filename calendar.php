<?php
session_start();

// Check if the user is logged in
if (!isset($_SESSION['admission_number'])) {
    // Redirect to the home page or login page if not logged in
    header("Location: Login.html");
    exit();
}

// Continue with the rest of the page content
?>




<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>School Event Manager</title>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;600&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.3/css/all.min.css">
    <style>
        :root {
            --primary: #2c3e50;
            --secondary: #3498db;
            --accent: #e74c3c;
            --light: #ecf0f1;
        }

        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
            font-family: 'Poppins', sans-serif;
        }

        body {
            padding-top: 160px;
            background: #f8f9fa;
        }

        header {
            background: linear-gradient(135deg, var(--primary), #1a252f);
            color: white;
            padding: 1rem;
            position: fixed;
            width: 100%;
            top: 0;
            z-index: 1000;
            box-shadow: 0 4px 15px rgba(0,0,0,0.1);
        }

        .logo h1 {
            background: linear-gradient(45deg, var(--secondary), var(--accent));
            -webkit-background-clip: text;
            background-clip: text;
            color: transparent;
            font-size: 2.5rem;
            letter-spacing: 1px;
            text-shadow: 2px 2px 4px rgba(0,0,0,0.1);
            animation: gradientShift 5s ease infinite;
            background-size: 200% 200%;
        }

        @keyframes gradientShift {
            0% { background-position: 0% 50%; }
            50% { background-position: 100% 50%; }
            100% { background-position: 0% 50%; }
        }

        .top-nav {
            top: 60px;
            background: rgba(255,255,255,0.85);
            z-index: 1001;
        }

        body {
            padding-top: 120px;
        }

        .logo {
            display: flex;
            align-items: center;
            gap: 1rem;
            max-width: 1200px;
            margin: 0 auto;
        }

        .logo img {
            height: 50px;
            filter: drop-shadow(0 2px 4px rgba(0,0,0,0.2));
        }

        .top-nav {
            background: rgba(255,255,255,0.95);
            padding: 1rem;
            position: fixed;
            top: 80px;
            width: 100%;
            backdrop-filter: blur(10px);
            border-bottom: 1px solid rgba(0,0,0,0.1);
        }

        .top-nav ul {
            display: flex;
            justify-content: center;
            gap: 1.5rem;
            list-style: none;
            max-width: 1200px;
            margin: 0 auto;
        }

        .top-nav a {
            color: var(--primary);
            text-decoration: none;
            padding: 0.5rem 1rem;
            border-radius: 5px;
            transition: all 0.3s;
            position: relative;
            font-weight: 500;
        }

        .top-nav a::after {
            content: '';
            position: absolute;
            bottom: -5px;
            left: 0;
            width: 0;
            height: 2px;
            background: var(--secondary);
            transition: width 0.3s;
        }

        .top-nav a:hover::after {
            width: 100%;
        }

        .calendar-container {
            max-width: 1000px;
            width: 100%;
            margin: 20px auto;
            padding: 2rem;
            background: white;
            border-radius: 15px;
            box-shadow: 0 8px 20px rgba(0,0,0,0.1);
            overflow: hidden;
        }

        .calendar-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 2rem;
        }

        .calendar-header h2 {
            color: var(--primary);
            font-size: 1.5rem;
        }

        .calendar-nav button {
            background: var(--secondary);
            border: none;
            padding: 0.5rem 1rem;
            cursor: pointer;
            font-size: 1.2rem;
            color: white;
            border-radius: 5px;
            transition: all 0.3s;
        }

        .calendar-nav button:hover {
            background: var(--accent);
        }

        .calendar-grid {
            display: grid;
            grid-template-columns: repeat(7, 1fr);
            gap: 10px;
        }

        .calendar-day-header {
            padding: 1rem;
            text-align: center;
            background: var(--light);
            color: var(--primary);
            font-weight: 600;
            border-radius: 8px;
        }

        .calendar-day {
            position: relative;
            min-height: 120px;
            padding: 1rem;
            background: white;
            border: 2px solid var(--light);
            border-radius: 8px;
            transition: all 0.3s;
            cursor: pointer;
        }

        .calendar-day:hover {
            transform: translateY(-3px);
            box-shadow: 0 4px 10px rgba(0,0,0,0.1);
        }

        .day-number {
            position: absolute;
            top: 5px;
            right: 5px;
            font-weight: bold;
            color: var(--primary);
        }

        .event-dot {
            width: 8px;
            height: 8px;
            background: var(--secondary);
            border-radius: 50%;
            display: inline-block;
            margin: 2px;
        }

        .event-list {
            margin-top: 25px;
            font-size: 0.9rem;
        }

        .event-item {
            padding: 5px;
            margin: 3px 0;
            background: var(--light);
            border-radius: 4px;
            cursor: pointer;
            transition: all 0.2s;
        }

        .event-item:hover {
            background: var(--secondary);
            color: white;
        }

        .current-day {
            border-color: var(--accent);
            background: #fee;
        }

        .modal {
            display: none;
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: rgba(0,0,0,0.5);
            justify-content: center;
            align-items: center;
            z-index: 1002; /* Increased z-index to ensure it's on top */
        }

        .modal-content {
            background: white;
            padding: 2rem;
            border-radius: 15px;
            max-width: 500px;
            width: 90%;
            box-shadow: 0 8px 20px rgba(0,0,0,0.2);
        }

        .add-event-form {
            display: flex;
            flex-direction: column;
            gap: 10px;
            margin-top: 20px;
        }

        .add-event-form input, .add-event-form button {
            padding: 10px;
            border-radius: 5px;
            border: 1px solid var(--light);
        }

        .add-event-form button {
            background: var(--secondary);
            color: white;
            cursor: pointer;
            transition: all 0.3s;
        }

        .add-event-form button:hover {
            background: var(--accent);
        }

        .close-button {
            margin-top: 20px;
            padding: 10px 20px;
            background: var(--secondary);
            color: whitesmoke;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            transition: all 0.3s;
        }

        .close-button:hover {
            background: var(--accent);
        }

        .edit-button {
            margin-top: 10px;
            padding: 10px 20px;
            background: var(--accent);
            color: white;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            transition: all 0.3s;
        }

        .edit-button:hover {
            background: var(--secondary);
        }

        .footer {
            background: var(--primary);
            color: var(--light);
            padding: 40px 20px 20px;
            margin-top: 50px;
        }

        .footer-content {
            max-width: 1200px;
            margin: 0 auto;
            display: grid;
            grid-template-columns: repeat(4, 1fr);
            gap: 30px;
        }

        .footer-section h4 {
            color: var(--secondary);
            margin-bottom: 15px;
            font-size: 1.2rem;
        }

        .footer-section p, .footer-section a {
            color: var(--light);
            line-height: 1.6;
            font-size: 0.9rem;
        }

        .footer-section a {
            text-decoration: none;
            display: block;
            transition: color 0.3s;
        }

        .footer-section a:hover {
            color: var(--secondary);
        }

        .footer-bottom {
            text-align: center;
            padding-top: 30px;
            margin-top: 30px;
            border-top: 1px solid rgba(255,255,255,0.1);
        }

        /* Button container for event actions */
        .event-actions {
            display: flex;
            justify-content: space-between;
            margin-top: 15px;
        }

        .event-actions button {
            flex: 1;
            margin: 0 5px;
        }

        /* Hide form by default */
        #add-event-form {
            display: none;
        }

        /* Show add event button by default */
        #add-event-button {
            display: block;
        }
    </style>
</head>
<body>
    <header>
        <div class="logo">
            <h1>School Event Management System</h1>
        </div>
    </header>

    <nav class="top-nav">
        <ul>

            <li><a href="Home_page.html">Home</a></li>
            <li><a href="Dashboard.php">Dashboard</a></li>
            <li><a href="calendar.php">Calendar</a></li>
            <li><a href="Account.php">Account</a></li>

        </ul>
    </nav>

    <div class="calendar-container">
        <div class="calendar-header">
            <h2 id="current-month">March 2024</h2>
            <div class="calendar-nav">
                <button id="prev-month">&lt;</button>
                <button id="next-month">&gt;</button>
            </div>
        </div>

        <div class="calendar-grid">
            <div class="calendar-day-header">Sun</div>
            <div class="calendar-day-header">Mon</div>
            <div class="calendar-day-header">Tue</div>
            <div class="calendar-day-header">Wed</div>
            <div class="calendar-day-header">Thu</div>
            <div class="calendar-day-header">Fri</div>
            <div class="calendar-day-header">Sat</div>
            <!-- Calendar days will be populated by JavaScript -->
        </div>
    </div>

    <div id="event-modal" class="modal">
        <div class="modal-content">
            <h3 id="modal-date">Selected Date</h3>
            <div id="modal-events"></div>
            
            <!-- Add event button (shown by default) -->
            <button id="add-event-button" class="edit-button" style="margin-top: 20px;">Add New Event</button>
            
            <form class="add-event-form" id="add-event-form">
                <input type="hidden" id="event-id" name="event_id">
                <input type="date" id="event-date" name="event_date" required>
                <input type="text" id="event-title" name="event_title" placeholder="Event Title" required>
                <input type="time" id="event-time" name="event_time" required>
                <input type="text" id="event-venue" name="event_venue" placeholder="Event Venue" required>
                
                <div class="event-actions">
                    <button type="button" id="save-event-button">Save Event</button>
                    <button type="button" id="cancel-form-button" class="close-button">Cancel</button>
                </div>
            </form>
            
            <button class="close-button" id="close-modal-button" style="width: 100%; margin-top: 20px;">Close</button>
        </div>
    </div>

    <script>
        let currentDate = new Date();
let events = [];
let editingEventId = null;
let isEditing = false;

// DOM elements
const addEventForm = document.getElementById('add-event-form');
const addEventButton = document.getElementById('add-event-button');
const saveEventButton = document.getElementById('save-event-button');
const cancelFormButton = document.getElementById('cancel-form-button');
const closeModalButton = document.getElementById('close-modal-button');
const eventModal = document.getElementById('event-modal');

// Function to fetch events from the server
async function fetchEvents() {
    try {
        const response = await fetch('get_events.php');
        const data = await response.json();
        if (data.status === "error") {
            console.error(data.message);
            return [];
        }
        return data;
    } catch (error) {
        console.error("Error fetching events:", error);
        return [];
    }
}

// Function to generate the calendar grid
async function generateCalendar() {
    events = await fetchEvents();
    const calendarGrid = document.querySelector('.calendar-grid');
    const monthYear = document.getElementById('current-month');
    const year = currentDate.getFullYear();
    const month = currentDate.getMonth();

    // Clear existing calendar
    calendarGrid.innerHTML = '<div class="calendar-day-header">Sun</div>' +
        '<div class="calendar-day-header">Mon</div>' +
        '<div class="calendar-day-header">Tue</div>' +
        '<div class="calendar-day-header">Wed</div>' +
        '<div class="calendar-day-header">Thu</div>' +
        '<div class="calendar-day-header">Fri</div>' +
        '<div class="calendar-day-header">Sat</div>';

    // Set month/year header
    monthYear.textContent = new Intl.DateTimeFormat('en-US',
        { month: 'long', year: 'numeric' }).format(currentDate);

    // Get first/last days of month
    const firstDay = new Date(year, month, 1);
    const lastDay = new Date(year, month + 1, 0);
    const startDay = firstDay.getDay();
    const endDate = lastDay.getDate();

    // Create empty days for previous month
    for (let i = 0; i < startDay; i++) {
        calendarGrid.appendChild(createEmptyDay());
    }

    // Create days for current month
    for (let day = 1; day <= endDate; day++) {
        const dateStr = `${year}-${String(month + 1).padStart(2, '0')}-${String(day).padStart(2, '0')}`;
        const dayEvents = events.filter(e => e.event_date === dateStr);
        const dayElement = createDayElement(day, dayEvents, dateStr);
        calendarGrid.appendChild(dayElement);
    }
}

// Function to create a day element
function createDayElement(dayNumber, events, dateStr) {
    const dayElement = document.createElement('div');
    dayElement.className = 'calendar-day';
    dayElement.setAttribute('data-date', dateStr);
    dayElement.innerHTML = `
        <div class="day-number">${dayNumber}</div>
        <div class="event-list">
            ${events.map(event => `
                <div class="event-item" data-event-id="${event.id}">
                    <span class="event-dot"></span>
                    ${event.event_title}
                </div>
            `).join('')}
        </div>
    `;

    // Highlight current day
    const today = new Date();
    if (currentDate.getMonth() === today.getMonth() &&
        currentDate.getFullYear() === today.getFullYear() &&
        dayNumber === today.getDate()) {
        dayElement.classList.add('current-day');
    }

    dayElement.addEventListener('click', () => openModal(dateStr));

    // Add event listeners to each event item
    setTimeout(() => {
        const eventItems = dayElement.querySelectorAll('.event-item');
        eventItems.forEach(item => {
            item.addEventListener('click', (e) => {
                e.stopPropagation(); // Prevent day click
                const eventId = parseInt(item.getAttribute('data-event-id'));
                showEventDetail(dateStr, eventId);
            });
        });
    }, 0);

    return dayElement;
}

// Function to create an empty day element
function createEmptyDay() {
    const day = document.createElement('div');
    day.className = 'calendar-day';
    day.style.background = '#f8f9fa';
    return day;
}

// Function to show event details in the modal
function showEventDetail(date, eventId) {
    const event = events.find(e => e.id === eventId);
    if (!event) return;

    resetModalState();

    const modalDate = document.getElementById('modal-date');
    const modalEvents = document.getElementById('modal-events');

    modalDate.textContent = new Date(date).toDateString();
    modalEvents.innerHTML = `
        <div class="event-item">
            <h4>${event.event_title}</h4>
            <p>Time: ${event.event_time}</p>
            <p>Venue: ${event.event_venue}</p>
        </div>
        <button class="edit-button" id="edit-event-button" data-event-id="${event.id}">Edit Event</button>
    `;

    eventModal.style.display = 'flex';

    // Add event listener to edit button
    document.getElementById('edit-event-button').addEventListener('click', () => {
        editEvent(eventId);
    });
}

// Function to open the modal to add/view events
function openModal(date) {
    resetModalState();

    const modalDate = document.getElementById('modal-date');
    const modalEvents = document.getElementById('modal-events');

    modalDate.textContent = new Date(date).toDateString();
    document.getElementById('event-date').value = date;

    const dayEvents = events.filter(e => e.event_date === date);

    if (dayEvents.length > 0) {
        modalEvents.innerHTML = `<h4>Events on this day:</h4>`;
        dayEvents.forEach(event => {
            modalEvents.innerHTML += `
                <div class="event-item" data-event-id="${event.id}">
                    <h4>${event.event_title}</h4>
                    <p>Time: ${event.event_time}</p>
                    <p>Venue: ${event.event_venue}</p>
                    <button class="edit-button" onclick="editEvent(${event.id})">Edit</button>
                </div>
            `;
        });
    } else {
        modalEvents.innerHTML = `<p>No events scheduled for this day.</p>`;
    }

    eventModal.style.display = 'flex';
}

// Reset modal to its default state
function resetModalState() {
    addEventForm.style.display = 'none';
    addEventButton.style.display = 'block';
    document.getElementById('event-id').value = '';
    addEventForm.reset();
    isEditing = false;
    editingEventId = null;
}

// Function to edit an event
function editEvent(eventId) {
    const event = events.find(e => e.id === eventId);
    if (!event) return;

    // Set form values
    document.getElementById('event-id').value = event.id;
    document.getElementById('event-date').value = event.event_date;
    document.getElementById('event-title').value = event.event_title;
    document.getElementById('event-time').value = event.event_time;
    document.getElementById('event-venue').value = event.event_venue;

    // Show form and hide add button
    addEventForm.style.display = 'flex';
    addEventButton.style.display = 'none';

    // Set editing state
    isEditing = true;
    editingEventId = event.id;
}

// Function to save an event
async function saveEvent() {
    const eventId = document.getElementById('event-id').value;
    const eventDate = document.getElementById('event-date').value;
    const eventTitle = document.getElementById('event-title').value;
    const eventTime = document.getElementById('event-time').value;
    const eventVenue = document.getElementById('event-venue').value;

    // Log the data to ensure it's captured correctly
    console.log({ eventId, eventDate, eventTitle, eventTime, eventVenue });

    if (isEditing && eventId) {
        // Update existing event
        await updateEvent(eventId, eventDate, eventTitle, eventTime, eventVenue);
    } else {
        // Add new event
        await addEvent(eventDate, eventTitle, eventTime, eventVenue);
    }

    closeModal();
    generateCalendar();
}

// Function to update an event
async function updateEvent(eventId, eventDate, eventTitle, eventTime, eventVenue) {
    try {
        const response = await fetch('event_handler.php', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/x-www-form-urlencoded',
            },
            body: new URLSearchParams({
                event_id: eventId,
                event_date: eventDate,
                event_title: eventTitle,
                event_time: eventTime,
                event_venue: eventVenue,
            }),
        });
        const result = await response.json();
        if (result.status === "success") {
            alert("Event updated successfully!");
        } else {
            console.error("Error updating event:", result.message);
        }
    } catch (error) {
        console.error("Error updating event:", error);
    }
}

// Function to add a new event
async function addEvent(eventDate, eventTitle, eventTime, eventVenue) {
    try {
        const response = await fetch('add_event.php', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/x-www-form-urlencoded',
            },
            body: new URLSearchParams({
                event_date: eventDate,
                event_title: eventTitle,
                event_time: eventTime,
                event_venue: eventVenue,
            }),
        });
        const result = await response.json();
        if (result.status === "success") {
            alert("Event added successfully!");
        } else {
            console.error("Error adding event:", result.message);
        }
    } catch (error) {
        console.error("Error adding event:", error);
    }
}

// Function to close the modal
function closeModal() {
    eventModal.style.display = 'none';
    resetModalState();
}

// Event Listeners
addEventButton.addEventListener('click', () => {
    addEventForm.style.display = 'flex';
    addEventButton.style.display = 'none';
});

saveEventButton.addEventListener('click', saveEvent);

cancelFormButton.addEventListener('click', () => {
    addEventForm.style.display = 'none';
    addEventButton.style.display = 'block';
    addEventForm.reset();
});

closeModalButton.addEventListener('click', closeModal);

// Navigation handlers for previous and next month
document.getElementById('prev-month').addEventListener('click', () => {
    currentDate.setMonth(currentDate.getMonth() - 1);
    generateCalendar();
});

document.getElementById('next-month').addEventListener('click', () => {
    currentDate.setMonth(currentDate.getMonth() + 1);
    generateCalendar();
});

// Define editEvent in global scope for use in onclick handlers
window.editEvent = editEvent;

// Initialize the calendar on page load
document.addEventListener('DOMContentLoaded', () => {
    generateCalendar();
});

    </script>
    
    <footer class="footer">
        <div class="footer-content">
            <div class="footer-section">
                <h4>Contact Us</h4>
                <p>Email: eschoolventsystem@gmail.com</p>
                <p>Phone: (+254) 7-1234-5678</p>
                <p>Address: 123 Campus Road, Nairobi City</p>
            </div>

            <div class="footer-section">
                <h4>Quick Links</h4>
                <ul>
                    <li><a href="Login.html">Log-In</a></li>
                    <li><a href="signup.html">Sign-Up</a></li>
                    <li><a href="Dashboard.html">Dashboard</a></li>
                </ul>
            </div>
        </div>

        <div class="footer-bottom">
            <p>© <span id="year"></span> School Event Management System. All rights reserved.</p>
            <script>document.getElementById('year').textContent = new Date().getFullYear();</script>
        </div>
    </footer>
</body>
</html>