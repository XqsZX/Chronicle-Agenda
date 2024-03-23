let currentDate = new Date();
let currentSelectedDay;
let events = [];
let currentEditingEventId = null;

// init the calendar and bind button
function initCalendar() {
    renderCalendar(currentDate);
    document.getElementById("prev-month").addEventListener("click", () => {
        currentDate.setMonth(currentDate.getMonth() - 1);
        renderCalendar(currentDate);
    });
    document.getElementById("next-month").addEventListener("click", () => {
        currentDate.setMonth(currentDate.getMonth() + 1);
        renderCalendar(currentDate);
    });
    fetchEvents();
}

function renderCalendar(date) {
    const calendar = document.querySelector("#calendar");
    calendar.innerHTML = ''; // clear current calendar

    // create table
    const table = document.createElement("table");
    const thead = document.createElement("thead");
    const tbody = document.createElement("tbody");
    table.appendChild(thead);
    table.appendChild(tbody);

    // create weekdays
    const daysOfWeek = ['Sun', 'Mon', 'Tue', 'Wed', 'Thu', 'Fri', 'Sat'];
    let headerRow = document.createElement("tr");
    daysOfWeek.forEach(day => {
        let dayHeader = document.createElement("th");
        dayHeader.textContent = day;
        headerRow.appendChild(dayHeader);
    });
    thead.appendChild(headerRow);

    let dateRow = document.createElement("tr");
    tbody.appendChild(dateRow);

    const firstDayIndex = new Date(date.getFullYear(), date.getMonth(), 1).getDay();
    const daysInMonth = new Date(date.getFullYear(), date.getMonth() + 1, 0).getDate();

    // padding the space
    for (let i = 0; i < firstDayIndex; i++) {
        dateRow.appendChild(document.createElement("td"));
    }

    // padding days
    for (let day = 1; day <= daysInMonth; day++) {
        if (dateRow.children.length === 7) {
            dateRow = document.createElement("tr");
            tbody.appendChild(dateRow);
        }

        let dayCell = document.createElement("td");
        dayCell.textContent = day;

        // check and show the event
        const eventDateStr = `${date.getFullYear()}-${('0' + (date.getMonth() + 1)).slice(-2)}-${('0' + day).slice(-2)}`;
        const dayEvents = events.filter(event => event.event_date === eventDateStr);
        dayEvents.forEach(event => {
            const eventDiv = document.createElement("div");
            eventDiv.className = 'event'; 
            eventDiv.textContent = `${event.title} at ${event.event_time}`;
            // set red or initial by is_highlighted
            eventDiv.style.color = event.is_highlighted ? 'red' : 'initial';

            // add share button
            const shareButton = document.createElement("button");
            shareButton.textContent = "Share";
            shareButton.onclick = () => shareEvent(event);
            
            // add edit button
            const editButton = document.createElement("button");
            editButton.textContent = "Edit";
            editButton.onclick = () => editEvent(event);

            // add delete button
            const deleteButton = document.createElement("button");
            deleteButton.textContent = "Delete";
            deleteButton.onclick = () => deleteEvent(event.event_id);

            // add click to change is_highlighted
            eventDiv.addEventListener('click', function() {
                toggleHighlight(event.event_id, !event.is_highlighted);
            });

            // add button into div
            eventDiv.appendChild(editButton);
            eventDiv.appendChild(deleteButton);
            eventDiv.appendChild(shareButton);

            dayCell.appendChild(eventDiv);
        });

        dateRow.appendChild(dayCell);
    }

    // renew date and year
    const monthYearDisplay = document.getElementById("month-year-display");
    monthYearDisplay.textContent = `${date.toLocaleString('en-US', { month: 'long', year: 'numeric' })}`;

    calendar.appendChild(table);
}

// add event dialog
function addEventDialog(day, date) {
    currentSelectedDay = new Date(date.getFullYear(), date.getMonth(), day);
    document.getElementById('addEventModal').style.display = 'block';
}

function saveEvent() {
    const title = document.getElementById('eventTitle').value;
    const event_date = document.getElementById('eventDate').value;
    const event_time = document.getElementById('eventTime').value;

    // build string to send
    const data = `title=${encodeURIComponent(title)}&event_date=${encodeURIComponent(event_date)}&event_time=${encodeURIComponent(event_time)}`;

    // send AJAX
    const xhr = new XMLHttpRequest();
    xhr.open("POST", "addEvent.php", true);
    xhr.setRequestHeader("Content-Type", "application/x-www-form-urlencoded");
    xhr.onload = function() {
        if (this.status === 200) {
            const response = JSON.parse(this.responseText);
            if (response.status === 'success') {
                closeModal();
                fetchEvents();
            } else {
                alert('Error: ' + response.message);
            }
        } else {
            alert('Error saving the event.');
        }
    };
    xhr.send(data);
}

function closeModal() {
    document.getElementById('addEventModal').style.display = 'none';
}

function fetchEvents() {
    const xhr = new XMLHttpRequest();
    xhr.open('GET', 'fetchEvents.php', true);
    xhr.onload = function() {
        if (this.status === 200) {
            try {
                const response = JSON.parse(this.responseText);
                if (response.status === 'success') {
                    events = response.events;
                    renderCalendar(currentDate);
                } else {
                    console.error('Error fetching events: ' + response.message);
                }
            } catch (error) {
                console.error('Error parsing response: ' + error);
            }
        } else {
            console.error('Error fetching events, HTTP Status: ' + this.status);
        }
    };
    xhr.send();
}

function editEvent(event) {
    console.log('Editing event:', event);

    document.getElementById('eventTitle').value = event.title;
    document.getElementById('eventDate').value = event.event_date;
    document.getElementById('eventTime').value = event.event_time;
    // store event_id
    currentEditingEventId = event.event_id;
    // show modal
    document.getElementById('editEventModal').style.display = 'block';
}

function deleteEvent(eventId) {
    console.log('Deleting event with ID:', eventId);
    if(confirm('Are you sure you want to delete this event?')) {
        // send AJAX
        const xhr = new XMLHttpRequest();
        xhr.open('POST', 'deleteEvent.php', true); 
        xhr.setRequestHeader('Content-Type', 'application/x-www-form-urlencoded');
        xhr.onload = function() {
            if (this.status === 200) {
                const response = JSON.parse(this.responseText);
                if (response.status === 'success') {
                    alert('Event deleted successfully.');
                    fetchEvents(); 
                } else {
                    alert('Failed to delete event: ' + response.message);
                }
            }
        };
        xhr.send('event_id=' + eventId);
    }
}

function updateEvent() {
    // get data
    const title = document.getElementById('editEventTitle').value;
    const date = document.getElementById('editEventDate').value;
    const time = document.getElementById('editEventTime').value;

    const data = `event_id=${currentEditingEventId}&title=${encodeURIComponent(title)}&event_date=${encodeURIComponent(date)}&event_time=${encodeURIComponent(time)}`;

    // send AJAX
    const xhr = new XMLHttpRequest();
    xhr.open('POST', 'updateEvent.php', true);
    xhr.setRequestHeader('Content-Type', 'application/x-www-form-urlencoded');
    xhr.onload = function() {
        if (this.status === 200) {
            const response = JSON.parse(this.responseText);
            if (response.status === 'success') {
                closeEditModal(); 
                fetchEvents(); 
            } else {
                alert('Failed to update event: ' + response.message);
            }
        } else {
            alert('Error updating the event.');
        }
    };
    xhr.send(data);
}

function closeEditModal() {
    document.getElementById('editEventModal').style.display = 'none';
}

function shareEvent(event) {
    // get user_id
    const sharedWithUserId = prompt("Enter the user ID to share this event with:");

    if(sharedWithUserId) {
        const data = `title=${encodeURIComponent(event.title)}&event_date=${encodeURIComponent(event.event_date)}&event_time=${encodeURIComponent(event.event_time)}&shared_with=${encodeURIComponent(sharedWithUserId)}`;

        const xhr = new XMLHttpRequest();
        xhr.open('POST', 'shareEvent.php', true);
        xhr.setRequestHeader('Content-Type', 'application/x-www-form-urlencoded');
        xhr.onload = function() {
            if (this.status === 200) {
                const response = JSON.parse(this.responseText);
                if (response.status === 'success') {
                    alert('Event shared successfully.');
                } else {
                    alert('Failed to share event: ' + response.message);
                }
            } else {
                alert('Error sharing the event.');
            }
        };
        xhr.send(data);
    }
}

function toggleHighlight(eventId, shouldBeHighlighted) {
    // send AJAX
    const xhr = new XMLHttpRequest();
    xhr.open('POST', 'toggleHighlightEvent.php', true);
    xhr.setRequestHeader('Content-Type', 'application/x-www-form-urlencoded');
    xhr.onload = function() {
        if (this.status === 200) {
            const response = JSON.parse(this.responseText);
            if (response.status === 'success') {
                fetchEvents();
            } else {
                alert('Failed to toggle highlight: ' + response.message);
            }
        } else {
            alert('Error toggling the event highlight.');
        }
    };
    xhr.send(`event_id=${eventId}&is_highlighted=${shouldBeHighlighted}`);
}


// init when load page
document.addEventListener('DOMContentLoaded', function() {
    initCalendar();

    document.getElementById("add-event").addEventListener("click", function() {
        document.getElementById('addEventModal').style.display = 'block';
    });
});

