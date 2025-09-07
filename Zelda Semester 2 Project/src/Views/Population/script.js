let discipline = null;
let period = null;
let year = null;
let allStudents = []; 
let allCourses = [];

$(document).ready(function() {
  discipline = getQueryParameterValue("discipline");
  period = getQueryParameterValue("period");
  year = getQueryParameterValue("year");

  console.log("Discipline:", discipline, "Period:", period, "Year:", year);

  if (!discipline || !period || !year) {
    window.location.replace("http://localhost/src/Views/Home");
  } else {
    fetchData(discipline, period, year); 
  }

  initializeEventListeners();
});

function fetchData(discipline, period, year) {
  fetchStudents(discipline, period, year);
  fetchCourses(discipline, period, year);
}

function fetchStudents(discipline, period, year) {
  const studentUrl = '../../get_students_by_discipline.php';

  console.log("Fetching students...");

  $('.loader').removeClass('d-none');

  $.ajax({
    url: studentUrl,
    type: 'POST',
    contentType: 'application/x-www-form-urlencoded',
    data: { discipline: discipline, year: year, period: period },
    dataType: 'json',
    success: function(response) {
      console.log("Students data fetched:", response);
      allStudents = response.students;
      updateStudentsTable(allStudents);
      $('#population-title').text(`Population - ${discipline}`);
      checkIfAllDataFetched();
    },
    error: function(jqXHR, textStatus, errorThrown) {
      console.error('Error fetching student data: ', textStatus, errorThrown);
      $('.loader').addClass('d-none');
    }
  });
}

function fetchCourses(discipline, period, year) {
  console.log("Fetching courses...");

  $.ajax({
    url: '../../get_courses.php',
    type: 'POST',
    contentType: 'application/x-www-form-urlencoded',
    data: { discipline: discipline, year: year, period: period },
    dataType: 'json',
    success: function(response) {
      console.log("Courses data fetched:", response);
      if (response.success) {
        allCourses = response.courses;
        updateCoursesTable(allCourses);
        checkIfAllDataFetched();
      } else {
        console.error('Error fetching courses: ', response.error);
      }
    },
    error: function(jqXHR, textStatus, errorThrown) {
      console.error('Error fetching courses: ', textStatus, errorThrown);
    }
  });
}

let dataFetchedCount = 0;
function checkIfAllDataFetched() {
  dataFetchedCount++;
  if (dataFetchedCount === 2) { 
    $('.loader').addClass('d-none');
  }
}

const updateStudentsTable = (students) => {
  const studentTableBody = $('.table-responsive:first .table tbody');
  studentTableBody.empty(); 

  students.forEach(student => {
    const { contact_first_name, contact_last_name, passed, student_epita_email, id } = student;
    const newRow = `
  <tr data-id="${id}">
    <td>${student_epita_email}</td>
    <td>${contact_first_name}</td>
    <td>${contact_last_name}</td>
    <td>${passed}</td>
    <td>
      <button class="btn-edit btn btn-sm btn-primary" data-id="${id}">
        <i class="fa fa-pencil" aria-hidden="true"></i>
      </button>
      <button class="btn-delete btn btn-sm btn-danger" data-id="${student_epita_email}">
        <i class="fa fa-trash" aria-hidden="true"></i>
      </button>
    </td>
  </tr>
`;

    studentTableBody.append(newRow);
  });

  initializeRowEventListeners();
};

const updateCoursesTable = (courses) => {
  const courseTableBody = $('#courses-table tbody');
  courseTableBody.empty();

  courses.forEach(course => {
    const { course_code, course_name, duration, professor_name } = course;
    const newRow = `
      <tr>
        <td><a href="#" class="course-name" data-course-code="${course_code}">${course_code}</a></td>
        <td>${course_name}</td>
        <td>${duration}</td>
        <td>${professor_name}</td>
      </tr>
    `;
    courseTableBody.append(newRow);
  });

  initializeCourseRowEventListeners();
};

const initializeEventListeners = () => {
  $('#search-input').on('input', function() {
    const searchValue = $(this).val().toLowerCase(); 
    const filteredStudents = allStudents.filter(student => 
      student.contact_first_name.toLowerCase().includes(searchValue) || 
      student.contact_last_name.toLowerCase().includes(searchValue) || 
      student.student_epita_email.toLowerCase().includes(searchValue)
    );
    updateStudentsTable(filteredStudents);
  });

  $('#search-courses-input').on('input', function() {
    const searchValue = $(this).val().toLowerCase();
    const filteredCourses = allCourses.filter(course => 
      course.course_code.toLowerCase().includes(searchValue) || 
      course.course_name.toLowerCase().includes(searchValue)
    );
    updateCoursesTable(filteredCourses);
  });

  $('#add-student-btn').on('click', function() {
    console.log("Redirecting to add student page...");
    window.location.replace(`http://localhost/src/Views/Signup/`);
  });

  $('#add-course-btn').on('click', function() {
    console.log("Redirecting to add course page...");
    window.location.replace('http://localhost/src/Views/New_Courses/index.html');
  });
};

const initializeCourseRowEventListeners = () => {
  $('.course-name').off('click').on('click', function(e) {
    e.preventDefault();
    const courseCode = $(this).data('course-code');
    window.location.href = `http://localhost/src/Views/Grades/index.html?course_code=${courseCode}`;
  });
};

const initializeRowEventListeners = () => {
  $('.btn-edit').off('click').on('click', function() {
    const row = $(this).closest('tr'); 
    const studentId = $(this).data('id');

    const email = row.find('td:eq(0)').text(); 
    const firstName = row.find('td:eq(1)').text();
    const lastName = row.find('td:eq(2)').text();

    console.log("Editing student:", email, firstName, lastName);

    row.find('td:eq(1)').html(`<input type="text" class="form-control" value="${firstName}" id="edit-first-name">`);
    row.find('td:eq(2)').html(`<input type="text" class="form-control" value="${lastName}" id="edit-last-name">`);

    $(this).replaceWith(`
      <button class="btn-save btn btn-sm btn-success" data-id="${email}">
        <i class="fa fa-check" aria-hidden="true"></i>
      </button>
      <button class="btn-cancel btn btn-sm btn-warning" data-id="${email}">
        <i class="fa fa-times" aria-hidden="true"></i>
      </button>
    `);

    $('.btn-save').off('click').on('click', function() {
      const studentId = $(this).data('id');
      const newFirstName = $('#edit-first-name').val();
      const newLastName = $('#edit-last-name').val();

      console.log("Saving updated student details:", studentId, newFirstName, newLastName);

      updateStudentDetails(studentId, newFirstName, newLastName);

      row.find('td:eq(1)').text(newFirstName);
      row.find('td:eq(2)').text(newLastName);

      $(this).siblings('.btn-cancel').remove();
      $(this).replaceWith(`
        <button class="btn-edit btn btn-sm btn-primary" data-id="${studentId}">
          <i class="fa fa-pencil" aria-hidden="true"></i>
        </button>
      `);

      initializeRowEventListeners(); 
    });

    $('.btn-cancel').off('click').on('click', function() {
      console.log("Cancel editing student");

      row.find('td:eq(1)').text(firstName);
      row.find('td:eq(2)').text(lastName);

      $(this).siblings('.btn-save').remove();
      $(this).replaceWith(`
        <button class="btn-edit btn btn-sm btn-primary" data-id="${studentId}">
          <i class="fa fa-pencil" aria-hidden="true"></i>
        </button>
      `);

      initializeRowEventListeners(); 
    });
  });

  $('.btn-delete').off('click').on('click', function() {
    const studentId = $(this).data('id');
    console.log("Deleting student:", studentId);
    deleteStudentByEmail(studentId);
    $(this).closest('tr').remove();
  });
};

const updateStudentDetails = (studentId, firstName, lastName) => {
  console.log("Updating student in database:", studentId, firstName, lastName);

  $.ajax({
    url: '../../update_student_by_id.php',
    type: 'POST',
    contentType: 'application/x-www-form-urlencoded',
    data: { student_epita_email: studentId, first_name: firstName, last_name: lastName },
    dataType: 'json',
    success: function(response) {
      console.log("Student updated successfully:", response);
      alert('Student updated successfully');
    },
    error: function(jqXHR, textStatus, errorThrown) {
      console.error('Error updating student: ', textStatus, errorThrown);
    }
  });
};

const deleteStudentByEmail = (studentId) => {
  console.log("Deleting student in database:", studentId);

  $.ajax({
    url: '../../delete_student_by_id.php',
    type: 'POST',
    contentType: 'application/x-www-form-urlencoded',
    data: { student_epita_email: studentId },
    dataType: 'json',
    success: function(response) {
      console.log("Student deleted successfully:", response);
      alert('Student deleted successfully');
    },
    error: function(jqXHR, textStatus, errorThrown) {
      console.error('Error deleting student: ', textStatus, errorThrown);
    }
  });
};

const getQueryParameterValue = (parameterName) => {
  const url = window.location.href;
  const questionMarkIndex = url.indexOf("?");
  if (questionMarkIndex !== -1) {
    const queryString = url.substring(questionMarkIndex + 1);
    const queryParams = queryString.split("&");
    for (const param of queryParams) {
      const pair = param.split("=");
      if (pair[0] === parameterName) {
        return pair[1];
      }
    }
  }
  return null;
};
