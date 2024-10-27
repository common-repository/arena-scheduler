/**
 * Calculates the next time based on the input time.
 * @param {string} currentTime - The current time in "HH:mm" format.
 * @returns {string} The next time in "HH:mm" format.
 */
export const getNextTime = (currentTime) => {
  // Splitting the time string into hours and minutes and converting them to numbers
  const [hours, minutes] = currentTime.split(':').map(Number);

  // Determining the next minutes based on the current minutes
  const nextMinutes = minutes === 30 ? 0 : 30;

  // Determining the next hours based on the next minutes
  const nextHours = nextMinutes === 0 ? hours + 1 : hours;

  // Formatting the next time in HH:mm format with leading zeros if needed
  return `${String(nextHours).padStart(2, '0')}:${String(nextMinutes).padStart(2, '0')}`;
};

/**
 * Calculates the next time based on the input time and interval.
 * @param {string} currentTime - The current time in "HH:mm" format.
 * @param {string} intervalTime - The interval time in minutes ('15', '30', or '1').
 * @returns {string} The next time in "HH:mm" format.
 */
export const getNextTimeByInterval = (currentTime, intervalTime) => {
  const [hours, minutes] = currentTime.split(':').map(Number);

  let nextHours = hours;
  let nextMinutes = minutes;

  if (intervalTime === '15') {
    // For 15-minute intervals
    const intervalCount = Math.floor(minutes / 15) + 1;
    nextMinutes = (intervalCount * 15) % 60; // Calculating next minutes
    nextHours += Math.floor((intervalCount * 15) / 60); // Calculating next hours
  } else if (intervalTime === '30') {
    // For 30-minute intervals
    nextMinutes = minutes === 30 ? 0 : 30; // Determining next minutes
    nextHours = nextMinutes === 0 ? hours + 1 : hours; // Determining next hours
  } else if (intervalTime === '60') {
    // For 1-hour intervals
    nextHours = (hours + 1) % 24; // Calculating next hours, considering the next day
    nextMinutes = 0; // For hourly intervals, minutes will always be 00
  }

  // Formatting the next time in HH:mm format with leading zeros if needed
  const formattedHours = String(nextHours).padStart(2, '0');
  const formattedMinutes = String(nextMinutes).padStart(2, '0');

  return `${formattedHours}:${formattedMinutes}`;
};

/**
 * Extracts and formats start and end times from a timeslotId.
 * @param {string} timeslotId - The timeslot identifier.
 * @returns {string} The formatted time range in "HH:mm-HH:mm" format.
 */
export const getTimeFromTimeslotId = (timeslotId) => {
  // Extracting HHmm for the start time from the timeslotId
  const startTimeString = timeslotId.substring(8, 12);

  // Extracting HHmm for the end time from the timeslotId
  const endTimeString = timeslotId.substring(12, 16);

  // Extracting hours and minutes for the start time
  const startHours = startTimeString.substring(0, 2);
  const startMinutes = startTimeString.substring(2, 4);

  // Extracting hours and minutes for the end time
  const endHours = endTimeString.substring(0, 2);
  const endMinutes = endTimeString.substring(2, 4);

  // Returning the time range in the format HH:mm-HH:mm
  return `${startHours}:${startMinutes}-${endHours}:${endMinutes}`;
};

/**
 * Combines and formats start and end times from a slot.
 * @param {string} slot - The slot containing start and end times in "HH:mm-HH:mm" format.
 * @returns {string} The combined time without ':' in between.
 */
export const combinedTime = (slot) => {
  // Splitting the slot string into start and end times
  const [startTime, endTime] = slot.split('-');

  // Concatenating and formatting start and end times without ':' in between
  return `${startTime.replace(':', '') + endTime.replace(':', '')}`;
};
