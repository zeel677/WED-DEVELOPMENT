const $ = (selector) => document.querySelector(selector);
const $$ = (selector) => [...document.querySelectorAll(selector)];

const loginView = $("#loginView");
const appView = $("#appView");
const loginForm = $("#loginForm");
const detailsPanel = $("#detailsPanel");
const detailsTitle = $("#detailsTitle");
const detailsContent = $("#detailsContent");
const modal = $("#modal");
const toast = $("#toast");

let currentRole = "student";
let currentName = "";
let toastTimeout;
let slideIndex = 0;

const roleInfo = {
  student: {
    label: "STUDENT PORTAL",
    subtitle: "Your academic progress, all in one place.",
    stats: [
      ["Attendance", "87%", "Across enrolled subjects", "📅"],
      ["Current CGPA", "7.8", "Sample academic record", "📊"],
      ["Registered courses", "5", "This semester", "🎓"],
      ["Pending requests", "1", "Leave request status", "📨"]
    ],
    modules: [
      ["Attendance", "📅", "View attendance percentage and subject-wise records."],
      ["Course Selection", "🎓", "Register for available semester courses."],
      ["Timetable", "🗓️", "Check your weekly class schedule."],
      ["Results", "📊", "View marks, grades and sample GPA."],
      ["Study Material", "📚", "Access notes and learning resources."],
      ["Leave Management", "📨", "Apply for leave and view request status."],
      ["Fee Status", "💳", "Check the sample fee payment status."],
      ["Faculty Feedback", "💬", "Share feedback about your learning experience."],
      ["My Profile", "👤", "View or edit your personal information."],
      ["Contact Faculty", "✉️", "Send a message to a faculty member."]
    ]
  },
  faculty: {
    label: "FACULTY PORTAL",
    subtitle: "Manage classes, academic records and student requests.",
    stats: [
      ["Assigned courses", "4", "Sample teaching allocation", "🎓"],
      ["Students", "128", "Across assigned classes", "👥"],
      ["Pending leaves", "3", "Requests awaiting review", "📨"],
      ["Upcoming tests", "2", "Sample test schedule", "🗓️"]
    ],
    modules: [
      ["Attendance Portal", "📅", "Select a class and record attendance."],
      ["Gradebook / Results", "📊", "Enter sample marks for students."],
      ["Study Material", "📚", "Upload or share course resources."],
      ["Test Schedule", "🗓️", "Publish a test date and subject."],
      ["Leave Requests", "📨", "Review and approve or reject leave requests."],
      ["Student Messages", "✉️", "Interact with students."],
      ["My Profile", "👤", "View or edit faculty profile information."]
    ]
  },
  admin: {
    label: "ADMINISTRATOR PANEL",
    subtitle: "Manage users and keep campus information up to date.",
    stats: [
      ["Students", "1,240", "Sample registered users", "🎓"],
      ["Faculty", "84", "Sample faculty directory", "👩‍🏫"],
      ["Active courses", "42", "Across departments", "📚"],
      ["Notices", "6", "Published announcements", "📢"]
    ],
    modules: [
      ["User Management", "👥", "Add or edit student and faculty profiles."],
      ["Notice Board", "📢", "Create a campus-wide announcement."],
      ["Course Directory", "🎓", "Review the sample course directory."],
      ["Timetable Overview", "🗓️", "Review sample timetable information."],
      ["Feedback Overview", "💬", "Review submitted demo feedback."],
      ["My Profile", "👤", "View or edit administrator profile information."]
    ]
  }
};

const notices = [
  { date: "22 OCT 2026", title: "Mid-semester timetable", text: "Students should check the timetable module for the latest sample schedule." },
  { date: "20 OCT 2026", title: "Study material updated", text: "Faculty have shared new resources for selected courses." },
  { date: "18 OCT 2026", title: "Leave request reminder", text: "Submit leave requests through the Leave Management module." }
];

const slides = [
  { emoji: "🎓", cls: "visual-one", title: "Learn, grow, achieve", text: "Keep your academic journey organized with CampusConnect." },
  { emoji: "📚", cls: "visual-two", title: "Resources within reach", text: "Find study material and course information in one convenient place." },
  { emoji: "🌟", cls: "visual-three", title: "Make progress visible", text: "Review your academic tasks and stay informed about campus updates." }
];

function showToast(message) {
  toast.textContent = message;
  toast.classList.remove("hidden");
  clearTimeout(toastTimeout);
  toastTimeout = setTimeout(() => toast.classList.add("hidden"), 3200);
}

function escapeHTML(value) {
  return String(value).replace(/[&<>"']/g, char => ({
    "&": "&amp;", "<": "&lt;", ">": "&gt;", '"': "&quot;", "'": "&#39;"
  })[char]);
}

function renderStats() {
  $("#statsGrid").innerHTML = roleInfo[currentRole].stats.map(([label, value, note, icon]) => `
    <article class="stat-card">
      <div class="stat-top"><span>${label}</span><span class="stat-icon">${icon}</span></div>
      <strong>${value}</strong><small>${note}</small>
    </article>`).join("");
}

function renderModules() {
  const modules = roleInfo[currentRole].modules;
  $("#moduleCount").textContent = `${modules.length} modules`;
  $("#moduleGrid").innerHTML = modules.map(([name, icon, description]) => `
    <button class="module-card" data-module="${escapeHTML(name)}">
      <span class="module-icon">${icon}</span>
      <span><strong>${escapeHTML(name)}</strong><small>${escapeHTML(description)}</small></span>
    </button>`).join("");

  $$("#moduleGrid [data-module]").forEach(button => {
    button.addEventListener("click", () => openModule(button.dataset.module));
  });
}

function renderAnnouncements() {
  $("#announcementList").innerHTML = notices.slice(0, 3).map(item => `
    <article class="announcement">
      <time>${item.date}</time>
      <strong>${escapeHTML(item.title)}</strong>
      <p>${escapeHTML(item.text)}</p>
    </article>`).join("");
}

function login(event) {
  event.preventDefault();
  currentRole = $("#role").value;
  currentName = $("#userName").value.trim();
  if (!currentName || !$("#password").value) {
    showToast("Please enter your name or ID and password.");
    return;
  }

  loginView.classList.add("hidden");
  appView.classList.remove("hidden");
  $("#logoutBtn").classList.remove("hidden");
  $("#roleLabel").textContent = roleInfo[currentRole].label;
  $("#welcomeTitle").textContent = `Welcome, ${currentName}!`;
  $("#welcomeSubtitle").textContent = roleInfo[currentRole].subtitle;
  $("#todayDate").textContent = new Date().toLocaleDateString("en-IN", {
    weekday: "short", day: "numeric", month: "short", year: "numeric"
  });
  renderStats();
  renderModules();
  renderAnnouncements();
  showToast(`Logged in to the ${currentRole} demo dashboard.`);
  window.scrollTo({ top: 0, behavior: "smooth" });
}

function logout() {
  appView.classList.add("hidden");
  loginView.classList.remove("hidden");
  $("#logoutBtn").classList.add("hidden");
  detailsPanel.classList.add("hidden");
  loginForm.reset();
  currentName = "";
  showToast("You have logged out of the demo.");
  window.scrollTo({ top: 0, behavior: "smooth" });
}

function openModule(moduleName) {
  detailsTitle.textContent = moduleName;
  detailsContent.innerHTML = getModuleContent(moduleName);
  detailsPanel.classList.remove("hidden");
  detailsPanel.scrollIntoView({ behavior: "smooth", block: "start" });

  const form = detailsContent.querySelector("form");
  if (form) form.addEventListener("submit", handleModuleSubmit);
  detailsContent.querySelectorAll("[data-demo-action]").forEach(button => {
    button.addEventListener("click", () => {
      const action = button.dataset.demoAction;
      if (action === "approve") {
        button.textContent = "Approved";
        button.disabled = true;
        button.classList.add("status-pill");
        showToast("Demo leave request approved.");
      } else if (action === "reject") {
        button.textContent = "Rejected";
        button.disabled = true;
        showToast("Demo leave request rejected.");
      }
    });
  });
}

function getModuleContent(name) {
  const table = (headers, rows) => `<div class="details-content"><table class="data-table"><thead><tr>${headers.map(h => `<th>${h}</th>`).join("")}</tr></thead><tbody>${rows.map(row => `<tr>${row.map(cell => `<td>${cell}</td>`).join("")}</tr>`).join("")}</tbody></table></div>`;
  const formStart = `<form class="action-form">`;
  const inputField = (label, type, placeholder, required = true) => `<label>${label}<input type="${type}" placeholder="${placeholder}" ${required ? "required" : ""}></label>`;
  const textareaField = (label, placeholder) => `<label>${label}<textarea rows="3" placeholder="${placeholder}" required></textarea></label>`;
  const submit = `<button class="primary-btn" type="submit">Submit demo form</button></form>`;

  if (name === "Attendance" || name === "Attendance Portal") {
    return `<p class="muted">Sample attendance records. Faculty can use the demo action to mark attendance.</p>` +
      table(["Subject", "Classes attended", "Total classes", "Attendance"], [
        ["Computer Networks", "18", "20", "<span class='status-pill'>90%</span>"],
        ["Object-Oriented Programming", "16", "20", "80%"],
        ["Mathematics", "17", "20", "85%"],
        ["Data Structures", "17", "20", "85%"]
      ]) + (currentRole === "faculty" ? `${formStart}<label>Class / subject<select required><option>Computer Networks</option><option>OOP</option><option>Mathematics</option></select></label><label>Attendance date<input type="date" required></label><button class="primary-btn" type="submit">Save attendance (demo)</button></form>` : "");
  }
  if (name === "Course Selection" || name === "Course Directory") {
    return `${formStart}<p class="muted">Choose a sample course. Your selection is not saved to a database.</p><label>Available course<select required><option value="">Select a course</option><option>Computer Networks</option><option>Object-Oriented Programming</option><option>Data Structures</option><option>Discrete Mathematics</option></select></label><label>Semester<select required><option>Semester 3</option><option>Semester 4</option></select></label>${submit}`;
  }
  if (name === "Timetable" || name === "Timetable Overview") {
    return table(["Day", "09:00–10:00", "10:00–11:00", "11:15–12:15"], [
      ["Monday", "OOP", "Mathematics", "CN"],
      ["Tuesday", "DSA", "CN", "Practical"],
      ["Wednesday", "Mathematics", "OOP", "Library"]
    ]);
  }
  if (name === "Results" || name === "Gradebook / Results") {
    const rows = currentRole === "faculty" ?
      [["STU101", "Computer Networks", `<input aria-label="Marks for STU101" type="number" min="0" max="100" value="84">`], ["STU102", "Computer Networks", `<input aria-label="Marks for STU102" type="number" min="0" max="100" value="76">`]] :
      [["Computer Networks", "18 / 20", "A"], ["OOP", "15 / 20", "B+"], ["Mathematics", "17 / 20", "A"]];
    return `<p class="muted">Sample marks only. Faculty can edit the values below for demonstration.</p>` + table(currentRole === "faculty" ? ["Student ID", "Course", "Marks (editable)"] : ["Subject", "Marks", "Grade"], rows) + (currentRole === "faculty" ? `<button class="primary-btn" id="saveMarks" type="button">Save marks (demo)</button>` : "");
  }
  if (name === "Study Material") {
    return `<p class="muted">Sample learning resources for your courses.</p>` +
      table(["Resource", "Course", "Action"], [
        ["Unit 1 Notes.pdf", "Computer Networks", `<button class="secondary-btn resource-btn" data-resource="Unit 1 Notes.pdf">Open demo</button>`],
        ["Java OOP Summary.pdf", "OOP", `<button class="secondary-btn resource-btn" data-resource="Java OOP Summary.pdf">Open demo</button>`]
      ]) +
      (currentRole === "faculty" ? `${formStart}${inputField("Material title", "text", "e.g. Unit 2 Notes")}${inputField("Course name", "text", "e.g. OOP")}${submit}` : "");
  }
  if (name === "Leave Management" || name === "Leave Requests") {
    return (currentRole === "faculty" ?
      `<p class="muted">Review this sample leave request.</p><div class="empty-state"><strong>Student: STU102</strong><p>Reason: Medical appointment · 23 October 2026</p><div class="inline-actions"><button class="primary-btn" data-demo-action="approve">Approve</button><button class="secondary-btn" data-demo-action="reject">Reject</button></div></div>` :
      `${formStart}${inputField("From date", "date", "")}${inputField("To date", "date", "")}${textareaField("Reason for leave", "Enter your reason")}${submit}<p class="muted">Current sample status: <span class="status-pill">No new request</span></p>`);
  }
  if (name === "Faculty Feedback" || name === "Feedback Overview") {
    return `${formStart}<p class="muted">This form is a demonstration and does not submit anonymously to a server.</p><label>Faculty member<select required><option value="">Choose faculty</option><option>Faculty Member A</option><option>Faculty Member B</option></select></label><label>Rating<select required><option>5 - Excellent</option><option>4 - Good</option><option>3 - Average</option><option>2 - Needs improvement</option><option>1 - Poor</option></select></label>${textareaField("Feedback", "Write your feedback")}${submit}`;
  }
  if (name === "Fee Status") {
    return table(["Fee type", "Amount", "Status"], [
      ["Tuition fee", "₹45,000", "<span class='status-pill'>Paid (sample)</span>"],
      ["Library fee", "₹1,000", "<span class='status-pill'>Paid (sample)</span>"],
      ["Laboratory fee", "₹3,000", "Pending (sample)"]
    ]);
  }
  if (name === "My Profile" || name === "User Management") {
    return `${formStart}${inputField("Full name", "text", currentName)}${inputField("Email", "email", "name@example.com")}${inputField("Mobile number", "tel", "10-digit mobile number")}${currentRole === "admin" ? `<label>Role<select><option>Student</option><option>Faculty</option><option>Administrator</option></select></label>` : ""}${submit}`;
  }
  if (name === "Contact Faculty" || name === "Student Messages") {
    return `${formStart}<label>Recipient<select required><option value="">Choose recipient</option><option>Academic Faculty</option><option>Course Coordinator</option><option>Department Office</option></select></label>${inputField("Subject", "text", "Message subject")}${textareaField("Message", "Type your message")}${submit}`;
  }
  if (name === "Test Schedule") {
    return `${formStart}${inputField("Test title", "text", "e.g. Unit 2 Test")}${inputField("Course", "text", "e.g. OOP")}${inputField("Test date", "date", "")}${submit}`;
  }
  if (name === "Notice Board") {
    return `${formStart}${inputField("Notice title", "text", "Enter a short title")}${textareaField("Announcement", "Write the notice for campus users")}${submit}`;
  }
  if (name === "Course Directory") return `<p class="muted">Sample course directory.</p>`;
  return `<div class="empty-state"><strong>${escapeHTML(name)}</strong><p>This module is represented as a front-end demo. A backend and database would be needed to store or share real records.</p></div>`;
}

function handleModuleSubmit(event) {
  event.preventDefault();
  const form = event.currentTarget;
  if (!form.reportValidity()) return;
  showToast("Demo submitted successfully. No data was sent to a server.");
  if (detailsTitle.textContent === "Notice Board" && currentRole === "admin") {
    const title = form.querySelector('input[type="text"]')?.value.trim();
    const message = form.querySelector("textarea")?.value.trim();
    if (title && message) {
      notices.unshift({ date: new Date().toLocaleDateString("en-IN").toUpperCase(), title, text: message });
      renderAnnouncements();
      showToast("Demo notice added to this page's announcement feed.");
    }
  }
}

$("#loginForm").addEventListener("submit", login);
$("#logoutBtn").addEventListener("click", logout);
$("#closeNotice").addEventListener("click", () => $("#notice").classList.add("hidden"));
$("#closeDetails").addEventListener("click", () => detailsPanel.classList.add("hidden"));

$("#menuBtn").addEventListener("click", () => {
  const open = $("#topnav").classList.toggle("show");
  $("#menuBtn").setAttribute("aria-expanded", String(open));
});
$("#topnav").querySelectorAll("a").forEach(link => link.addEventListener("click", () => {
  $("#topnav").classList.remove("show");
  $("#menuBtn").setAttribute("aria-expanded", "false");
}));

$("#themeBtn").addEventListener("click", () => {
  document.body.classList.toggle("dark");
  const dark = document.body.classList.contains("dark");
  $("#themeBtn").textContent = dark ? "☀️ Light mode" : "🌙 Dark mode";
});

$$(".faq-question").forEach(button => {
  button.addEventListener("click", () => {
    const answer = button.nextElementSibling;
    const isOpen = answer.classList.toggle("open");
    button.setAttribute("aria-expanded", String(isOpen));
    button.querySelector("span").textContent = isOpen ? "−" : "＋";
  });
});

function openModal() {
  $("#modalTitle").textContent = "Campus announcements";
  $("#modalBody").innerHTML = notices.map(item => `
    <article class="announcement"><time>${item.date}</time><strong>${escapeHTML(item.title)}</strong><p>${escapeHTML(item.text)}</p></article>
  `).join("");
  modal.classList.remove("hidden");
  $("#closeModal").focus();
}
function closeModal() { modal.classList.add("hidden"); }
$("#showAllNotices").addEventListener("click", openModal);
$("#closeModal").addEventListener("click", closeModal);
$("#modalDone").addEventListener("click", closeModal);
modal.addEventListener("click", event => { if (event.target === modal) closeModal(); });
document.addEventListener("keydown", event => { if (event.key === "Escape") closeModal(); });

function renderSlide() {
  const slide = slides[slideIndex];
  $("#slideContent").innerHTML = `
    <div class="slide-visual ${slide.cls}">${slide.emoji}</div>
    <div><h3>${slide.title}</h3><p>${slide.text}</p></div>`;
  $("#sliderDots").innerHTML = slides.map((_, index) =>
    `<button class="slider-dot ${index === slideIndex ? "active" : ""}" aria-label="Go to slide ${index + 1}" data-slide="${index}"></button>`
  ).join("");
  $$("#sliderDots button").forEach(button => button.addEventListener("click", () => {
    slideIndex = Number(button.dataset.slide);
    renderSlide();
  }));
}
$("#nextSlide").addEventListener("click", () => { slideIndex = (slideIndex + 1) % slides.length; renderSlide(); });
$("#prevSlide").addEventListener("click", () => { slideIndex = (slideIndex - 1 + slides.length) % slides.length; renderSlide(); });

detailsContent.addEventListener("click", event => {
  const resource = event.target.closest("[data-resource]");
  if (resource) showToast(`Demo resource selected: ${resource.dataset.resource}`);
  if (event.target.id === "saveMarks") showToast("Sample marks saved for this page only.");
});
renderSlide();
