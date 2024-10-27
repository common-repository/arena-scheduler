import axios from "axios";

// Get the server host from environment variables
const serverHost = process.env.REACT_APP_SERVER_HOST;

// Attempt to read the nonce from the meta tag, or use an empty string if not found
const nonceMetaTag = document.querySelector('meta[name="api-nonce"]');
const nonce = nonceMetaTag ? nonceMetaTag.getAttribute('content') : '';

// Create an Axios instance with default configurations
const apiClient = axios.create({
  baseURL: serverHost,
  headers: {
    "Content-Type": "application/json",
    "X-WP-Nonce": nonce,
  },
  params: {
    _nonce: nonce
  },
});

// Function to fetch arena data
export const fetchArenaData = async () => {
  try {
    const response = await apiClient.get("arena");
    return response.data; // Return the data received from the API
  } catch (error) {
    console.error("Error fetching arena data:", error);
    throw error; // Re-throw the error to handle it where the function is called
  }
};

// Function to fetch arena category data
export const fetchArenaCategoryData = async () => {
  try {
    const response = await apiClient.get("arena/category");
    return response.data; // Return the data received from the API
  } catch (error) {
    console.error("Error fetching arena category data:", error);
    throw error; // Re-throw the error to handle it where the function is called
  }
};

// Function to fetch timesheet data
export const fetchTimesheetData = async (activeTab, startDate, endDate) => {
  try {
    const response = await apiClient.get("timesheet", {
      params: {
        active_tab: activeTab,
        start_date: startDate,
        end_date: endDate,
      },
    });
    return response.data; // Return the data received from the API
  } catch (error) {
    console.error("Error fetching timesheet data:", error);
    throw error; // Re-throw the error to handle it where the function is called
  }
};

// Function to copy the timesheet data for a week
export const copyTimesheetData = async (data) => {
  try {
    const response = await apiClient.post("arena/copy/schedule/week", data);
    return response.data; // Return the data received from the API
  } catch (error) {
    console.error("Error saving timeslot:", error);
    throw error; // Re-throw the error to handle it where the function is called
  }
};

// Function to create a new timeslot
export const createTimeslot = async (data) => {
  try {
    const response = await apiClient.post("arena/save/schedule", data);
    return response.data; // Return the data received from the API
  } catch (error) {
    console.error("Error saving timeslot:", error);
    throw error; // Re-throw the error to handle it where the function is called
  }
};

// Function to copy a timeslot
export const copyTimeslot = async (data) => {
  try {
    const response = await apiClient.post("arena/copy/schedule", data);
    return response.data; // Return the data received from the API
  } catch (error) {
    console.error("Error saving timeslot:", error);
    throw error; // Re-throw the error to handle it where the function is called
  }
};

// Function to save a comment for a timeslot
export const saveTimeslotComment = async (data) => {
  try {
    const response = await apiClient.post("arena/save/schedule/comment", data);
    return response.data; // Return the data received from the API
  } catch (error) {
    console.error("Error saving timeslot:", error);
    throw error; // Re-throw the error to handle it where the function is called
  }
};
