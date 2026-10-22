import { fetchStudents } from "./studentService.js";
import { renderStudents } from "./studentUI.js";

const searchInput = document.getElementById("searchInput");
const courseFilter = document.getElementById("courseFilter");
const sortSelect = document.getElementById("sortSelect");
const studentTable = document.getElementById("studentTable");
const status = document.getElementById("status");
const prevBtn = document.getElementById("prevBtn");
const nextBtn = document.getElementById("nextBtn");
const pageInfo = document.getElementById("pageInfo");

let students = [];
let currentPage = 1;
const pageSize = 5;

function getFilteredStudents() {
    const searchText = searchInput.value.toLowerCase().trim();
    const selectedCourse = courseFilter.value;

    let result = students.filter(function(student) {
        const matchesSearch =
            student.name.toLowerCase().includes(searchText) ||
            student.rollNo.toLowerCase().includes(searchText);

        const matchesCourse =
            selectedCourse === "all" ||
            student.course === selectedCourse;

        return matchesSearch && matchesCourse;
    });

    result.sort(function(a, b) {
        const comparison = a.name.localeCompare(b.name);

        return sortSelect.value === "asc"
            ? comparison
            : -comparison;
    });

    return result;
}

function updateView() {
    const filteredStudents = getFilteredStudents();
    const totalPages = Math.ceil(filteredStudents.length / pageSize);

    if (totalPages === 0) {
        currentPage = 1;
    } else if (currentPage > totalPages) {
        currentPage = totalPages;
    }

    const startIndex = (currentPage - 1) * pageSize;

    const pageStudents = filteredStudents.slice(
        startIndex,
        startIndex + pageSize
    );

    renderStudents(pageStudents, studentTable);

    status.textContent = filteredStudents.length === 0
        ? "No students found."
        : `Showing ${pageStudents.length} of ${filteredStudents.length} students`;

    pageInfo.textContent = `Page ${currentPage} of ${totalPages}`;

    prevBtn.disabled = currentPage === 1;
    nextBtn.disabled =
        totalPages === 0 || currentPage === totalPages;
}

searchInput.addEventListener("input", function() {
    currentPage = 1;
    updateView();
});

courseFilter.addEventListener("change", function() {
    currentPage = 1;
    updateView();
});

sortSelect.addEventListener("change", function() {
    currentPage = 1;
    updateView();
});

prevBtn.addEventListener("click", function() {
    if (currentPage > 1) {
        currentPage--;
        updateView();
    }
});

nextBtn.addEventListener("click", function() {
    const totalPages = Math.ceil(
        getFilteredStudents().length / pageSize
    );

    if (currentPage < totalPages) {
        currentPage++;
        updateView();
    }
});

async function init() {
    try {
        status.textContent = "Loading students...";

        students = await fetchStudents();

        updateView();
    } catch (error) {
        console.error("Error:", error);

        status.textContent =
            "Unable to load students. Check students.json and run using Live Server.";

        studentTable.innerHTML = "";
    }
}

init();