export function renderStudents(students, table) {
    table.innerHTML = "";

    students.forEach(function(student) {
        const row = document.createElement("tr");

        [
            student.rollNo,
            student.name,
            student.course,
            student.year,
            student.city
        ].forEach(function(value) {
            const cell = document.createElement("td");
            cell.textContent = value;
            row.appendChild(cell);
        });

        table.appendChild(row);
    });
}