
const API = "api.php";

const form = document.getElementById("studentForm");
const studentId = document.getElementById("studentId");
const nameInput = document.getElementById("name");
const emailInput = document.getElementById("email");
const courseInput = document.getElementById("course");
const table = document.getElementById("studentTable");
const message = document.getElementById("message");
const loading = document.getElementById("loading");
const search = document.getElementById("search");
const formTitle = document.getElementById("formTitle");
const saveBtn = document.getElementById("saveBtn");
const cancelBtn = document.getElementById("cancelBtn");

let debounceTimer;

function showMessage(text, type = "success") {
    message.textContent = text;
    message.className = type;
}

async function request(url, options = {}) {
    const response = await fetch(url, {
        ...options,
        headers: {
            ...(options.body ? { "Content-Type": "application/json" } : {}),
            ...(options.method && options.method !== "GET"
                ? { "X-CSRF-Token": window.API_CSRF }
                : {}),
            ...options.headers
        }
    });

    const data = await response.json().catch(() => null);

    if (!response.ok || !data || data.success !== true) {
        throw new Error(data?.message || `Request failed (${response.status}).`);
    }

    return data;
}

function makeCell(value) {
    const td = document.createElement("td");
    td.textContent = value ?? "";
    return td;
}

function renderStudents(students) {
    table.replaceChildren();

    if (!students.length) {
        const row = document.createElement("tr");
        const cell = makeCell("No students found.");
        cell.colSpan = 5;
        row.appendChild(cell);
        table.appendChild(row);
        return;
    }

    students.forEach(student => {
        const row = document.createElement("tr");

        row.appendChild(makeCell(student.id));
        row.appendChild(makeCell(student.name));
        row.appendChild(makeCell(student.email));
        row.appendChild(makeCell(student.course));

        const actions = document.createElement("td");

        const editBtn = document.createElement("button");
        editBtn.textContent = "Edit";
        editBtn.type = "button";
        editBtn.className = "secondary";
        editBtn.addEventListener("click", () => editStudent(student.id));

        const deleteBtn = document.createElement("button");
        deleteBtn.textContent = "Delete";
        deleteBtn.type = "button";
        deleteBtn.className = "danger";
        deleteBtn.addEventListener("click", () => deleteStudent(student.id));

        actions.append(editBtn, deleteBtn);
        row.appendChild(actions);
        table.appendChild(row);
    });
}

async function loadStudents(query = "") {
    loading.hidden = false;

    try {
        const data = await request(
            `${API}?q=${encodeURIComponent(query)}`
        );
        renderStudents(data.students);
    } catch (error) {
        table.replaceChildren();
        const row = document.createElement("tr");
        const cell = makeCell("Could not load student records.");
        cell.colSpan = 5;
        row.appendChild(cell);
        table.appendChild(row);
        showMessage(error.message, "error");
    } finally {
        loading.hidden = true;
    }
}

function resetForm() {
    form.reset();
    studentId.value = "";
    formTitle.textContent = "Add Student";
    saveBtn.textContent = "Add Student";
    cancelBtn.hidden = true;
}

async function editStudent(id) {
    try {
        const data = await request(`${API}?id=${encodeURIComponent(id)}`);
        const s = data.student;

        studentId.value = s.id;
        nameInput.value = s.name;
        emailInput.value = s.email;
        courseInput.value = s.course;

        formTitle.textContent = "Edit Student";
        saveBtn.textContent = "Update Student";
        cancelBtn.hidden = false;

        nameInput.focus();
    } catch (error) {
        showMessage(error.message, "error");
    }
}

form.addEventListener("submit", async event => {
    event.preventDefault();

    if (!form.reportValidity()) return;

    const id = studentId.value;
    const payload = {
        name: nameInput.value.trim(),
        email: emailInput.value.trim(),
        course: courseInput.value.trim()
    };

    saveBtn.disabled = true;

    try {
        const data = await request(API, {
            method: id ? "PUT" : "POST",
            body: JSON.stringify(id ? { ...payload, id: Number(id) } : payload)
        });

        showMessage(data.message);
        resetForm();
        await loadStudents(search.value.trim());
    } catch (error) {
        showMessage(error.message, "error");
    } finally {
        saveBtn.disabled = false;
    }
});

cancelBtn.addEventListener("click", resetForm);

async function deleteStudent(id) {
    if (!confirm("Are you sure you want to delete this student?")) {
        return;
    }

    try {
        const data = await request(API, {
            method: "DELETE",
            body: JSON.stringify({ id: Number(id) })
        });

        showMessage(data.message);
        await loadStudents(search.value.trim());
    } catch (error) {
        showMessage(error.message, "error");
    }
}

// Debounced search: wait 300 ms after typing stops.
search.addEventListener("input", () => {
    clearTimeout(debounceTimer);

    debounceTimer = setTimeout(() => {
        loadStudents(search.value.trim());
    }, 300);
});

// Initial fetch when the page opens.
loadStudents();
