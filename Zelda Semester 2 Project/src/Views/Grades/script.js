$(document).ready(function() {
  const courseCode = getQueryParameterValue('course_code');
  if (courseCode) {
    fetchGradesForCourse(courseCode);
  } else {
    console.error('No course code found in URL');
  }
});

function fetchGradesForCourse(courseCode) {
  $('.loader').removeClass('d-none');

  $.ajax({
    url: '../../get_grades.php',
    type: 'GET',
    dataType: 'json',
    data: { course_code: courseCode },  
    success: function(response) {
      const tableBody = $('#grades-table-body');
      tableBody.empty(); 

      if (response.success && response.grades.length > 0) {
        response.grades.forEach((item, index) => {
          const row = `
            <tr>
              <td>${index + 1}</td>
              <td>${item.email}</td>
              <td>${item.f_name}</td>
              <td>${item.l_name}</td>
              <td>${item.course}</td>
              <td>${item.grade_out_of_20}</td>
            </tr>
          `;
          tableBody.append(row);
        });
      } else {
        tableBody.append('<tr><td colspan="6" class="text-center">No grades found for this course.</td></tr>');
      }

      $('.loader').addClass('d-none');  
    },
    error: function(jqXHR, textStatus, errorThrown) {
      console.error('Error fetching grades:', textStatus, errorThrown);
      $('.loader').addClass('d-none');  
    }
  });
}

function getQueryParameterValue(parameterName) {
  const url = window.location.href;
  const queryString = url.split('?')[1];
  if (queryString) {
    const queryParams = new URLSearchParams(queryString);
    return queryParams.get(parameterName);
  }
  return null;
}
