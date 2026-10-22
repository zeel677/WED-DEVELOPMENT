export async function fetchStudents() {
    const response = await fetch("./students.json");

    if (!response.ok) {
        throw new Error("Failed to load student data.");
    }

    return await response.json();
}