function getCity(id, url, token) {
    if (!id) {
        console.error("State ID is missing");
        return;
    }

    $.ajax({
        url: url,
        method: 'POST',
        data: {
            state_id: id,
            _token: token
        },
        success: function(response) {
            let html = `<option selected value=''>Select City</option>`;

            if (Array.isArray(response.data)) {
                response.data.forEach((item) => {
                    html += `<option value="${item.id}">${item.name}</option>`;
                });
            }

            $('.appendcity').html(html);
        },
        error: function(xhr) {
            console.error("AJAX error:", xhr.responseText);
        }
    });
}

function getArea(id, url, token) {
    if (!id) {
        console.error("City ID is missing");
        return;
    }

    $.ajax({
        url: url,
        method: 'POST',
        data: {
            
            city_id: id,
            _token: token
        },
        success: function(response) {
            let html = `<option selected value=''>Select Area</option>`;

            if (Array.isArray(response.data)) {
                response.data.forEach((item) => {
                    html += `<option value="${item.id}">${item.name}</option>`;
                });
            }

            $('.appendarea').html(html);
        },
        error: function(xhr) {
            console.error("AJAX error:", xhr.responseText);
        }
    });
}



function getrenderComponent(id, url, token, page = 1) {
    $.ajax({
        url: url,
        type: 'GET',
        data: {
            user_id: id,
            page: page,
            _token: token
        },
        success: function(response) {
            $('#userCardContainer').html(response.html);
            $('.modal').modal('show'); // Keep modal open
        },
        error: function(err) {
            console.error(err);
        }
    });
}

$(document).on('click', '.modal .pagination a', function(e) {
    e.preventDefault();

    const pageUrl = $(this).attr('href');
    const page = new URL(pageUrl).searchParams.get("page");

    const $wrapper = $('#modalPaginationWrapper');
    const url = $wrapper.data('url');
    const token = $wrapper.data('token');
    const userId = $wrapper.data('user-id');

    if (!url || !userId) {
        console.error('Missing URL or user ID');
        return;
    }

    getrenderComponent(userId, url, token, page); // Now page is accepted
});

function getSubCategory(id, url, token) {
    console.log(id)
    if (!id) {
        console.error("State ID is missing");
        return;
    }

    $.ajax({
        url: url,
        method: 'POST',
        data: {
            category_id: id,
            _token: token
        },
        success: function(response) {
            let html = `<option selected value=''>choose...</option>`;

            if (Array.isArray(response.data)) {
                response.data.forEach((item) => {
                    html += `<option value="${item.id}">${item.name}</option>`;
                });
            }

            $('.appendsub').html(html);
        },
        error: function(xhr) {
            console.error("AJAX error:", xhr.responseText);
        }
    });
}

function getmultiplevarientFeild(url, token) {
   

    $.ajax({
        url: url,
        method: 'POST',
        data: {
            _token: token
        },
        success: function(response) {
            console.log(response)
            $('.appendvarient').append(response);
        },
        error: function(xhr) {
            console.error("AJAX error:", xhr.responseText);
        }
    });
}


function isLocationEnable(url,token){
    $.ajax({
        url: url,
        method: 'GET',
        data: {
            _token: token
        },
        success: function(response) {
            console.log(response)
            
        },
        error: function(xhr) {
            console.error("AJAX error:", xhr.responseText);
        }
    });
}


function renderOrderList(url,token,selectElement){
    const selectedValues = Array.from(selectElement.selectedOptions).map(option => option.value);
    const beatIds = selectedValues.filter(val => val !== '');
    $.ajax({
        url: url,
        method: 'POST',
        data: {
            _token: token,
            'beat_id':beatIds
        },
        success: function(response) {
            // console.log(response)
            $("#orderListAppend").html(response)
            
        },
        error: function(xhr) {
            console.error("AJAX error:", xhr.responseText);
        }
    });
}




