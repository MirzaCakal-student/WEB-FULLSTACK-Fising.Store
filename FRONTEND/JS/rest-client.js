/*finished*/
const RestClient = {
  
  /**
   * GET request
   */
  get: function(url, callback, error_callback) {
    $.ajax({
      url: Constants.PROJECT_BASE_URL + url,
      type: "GET",
      beforeSend: function(xhr) {
        const token = localStorage.getItem("user_token");
        if (token) {
          xhr.setRequestHeader("Authorization", "Bearer " + token);
        }
      },
      success: function(response) {
        if (callback) callback(response);
      },
      error: function(jqXHR, textStatus, errorThrown) {
        if (error_callback) {
          error_callback(jqXHR);
        } else {
          Utils.showToast(jqXHR.responseJSON?.message || 'Request failed', 'error');
        }
      }
    });
  },

  /**
   * Generic request method with proper JSON handling
   */
  request: function(url, method, data, callback, error_callback) {
    const ajaxOptions = {
      url: Constants.PROJECT_BASE_URL + url,
      type: method,
      contentType: "application/json",
      dataType: "json",
      beforeSend: function(xhr) {
        const token = localStorage.getItem("user_token");
        if (token) {
          xhr.setRequestHeader("Authorization", "Bearer " + token);
        }
      },
      success: function(response, status, jqXHR) {
        if (callback) callback(response);
      },
      error: function(jqXHR, textStatus, errorThrown) {
        if (error_callback) {
          error_callback(jqXHR);
        } else {
          Utils.showToast(jqXHR.responseJSON?.message || 'Request failed', 'error');
        }
      }
    };

    // Only add data if it exists and convert to JSON string
    if (data) {
      ajaxOptions.data = JSON.stringify(data);
    }

    $.ajax(ajaxOptions);
  },

  /**
   * POST request
   */
  post: function(url, data, callback, error_callback) {
    RestClient.request(url, "POST", data, callback, error_callback);
  },

  /**
   * PUT request
   */
  put: function(url, data, callback, error_callback) {
    RestClient.request(url, "PUT", data, callback, error_callback);
  },

  /**
   * PATCH request
   */
  patch: function(url, data, callback, error_callback) {
    RestClient.request(url, "PATCH", data, callback, error_callback);
  },

  /**
   * DELETE request
   */
  delete: function(url, data, callback, error_callback) {
    RestClient.request(url, "DELETE", data, callback, error_callback);
  }
};