import Modal from "react-bootstrap/Modal";
import Button from "react-bootstrap/Button";
import Spinner from "react-bootstrap/Spinner";
import { useEffect, useState, useCallback } from "react";
import { getNextTimeByInterval, combinedTime } from "../../utils/helper";
import { createTimeslot, copyTimeslot } from "../../utils/api";

const BookSlotModal = ({
	showBookArenaTimeSlotModal,
	handleCloseBookArenaTimeSlotModal,
	activeTab,
	day,
	slot,
	category,
	activeArena,
	refreshTimesheetData,
}) => {
	// State to manage the loading state while submitting
	const [loading, setLoading] = useState(false);

	// State to store the selected time slot
	const [selectedSlot, setSelectedSlot] = useState("");

	// State to store generated time slots
	const [timeSlots, setTimeSlots] = useState([]);

	// Split the time slot into start and end times
	const [startTime, endTime] = slot.split("-");

	// Format the date
	const formattedDate = day.replace(/-/g, "");

	// Combine the date and time
	const combinedValue =
		formattedDate + startTime.replace(":", "") + endTime.replace(":", "");

	// Function to generate time slots based on the start time
	const generateTimeSlots = useCallback((startTime) => {
		const endTime =
			activeArena.end_time !== undefined &&
				activeArena.end_time !== null &&
				activeArena.end_time !== ""
				? activeArena.end_time
				: "21:00";
		const intervalTime =
			activeArena.interval_time !== undefined &&
				activeArena.interval_time !== null &&
				activeArena.interval_time !== ""
				? activeArena.interval_time
				: "30";
		const slots = [];

		let currentTime = startTime;

		while (currentTime !== endTime) {
			const nextSlot = getNextTimeByInterval(currentTime, intervalTime);
			slots.push(`${currentTime}-${nextSlot}`);
			currentTime = nextSlot;
		}

		setTimeSlots(slots);
	}, []);

	useEffect(() => {
		generateTimeSlots(startTime);
	}, [generateTimeSlots, startTime]);

	// Function to handle saving the selected time slot
	const handleCreateTimeslot = async () => {
		const data = {
			arena_id: activeTab,
			category: category,
			scheduled_date: day,
			timeslot_id: combinedValue,
		};

		try {
			const response = await createTimeslot(data); // Use the function from utils/api
			if (response.status === 1) {
				refreshTimesheetData(response.data);
			}
		} catch (error) {
			console.error("Error saving timeslot:", error);
		}
	};

	const handleCopyTimeslot = async () => {
		setLoading(true);

		const data = {
			arena_id: activeTab,
			category: category,
			scheduled_date: day,
			timeslot_id: combinedValue,
			end_timeslot: selectedSlot,
			interval_time: activeArena.interval_time,
		};

		try {
			const response = await copyTimeslot(data); // Use the function from utils/api
			refreshTimesheetData(response);
			handleCloseBookArenaTimeSlotModal();
		} catch (error) {
			console.error("Error saving timeslot:", error);
		} finally {
			setLoading(false);
		}
	};

	const handleSlotChange = (event) => {
		setSelectedSlot(combinedTime(event.target.value));
	};

	useEffect(() => {
		if (showBookArenaTimeSlotModal === true) {
			handleCreateTimeslot(); // Invoke the handleCreateTimeslot function
		}
	}, [showBookArenaTimeSlotModal]);

	return (
		<Modal
			show={showBookArenaTimeSlotModal}
			onHide={handleCloseBookArenaTimeSlotModal}
			size="md"
			aria-labelledby="contained-modal-title-vcenter"
			centered
		>
			<Modal.Header closeButton>
				<Modal.Title>Välj hur länge aktiviteten pågår</Modal.Title>
			</Modal.Header>

			<Modal.Body>
				<select
					className={"form-select"}
					onChange={handleSlotChange}
				>
					{timeSlots.map((slot, index) => (
						<option key={index} value={slot}>
							{slot}
						</option>
					))}
				</select>
			</Modal.Body>

			<Modal.Footer>
				<Button
					onClick={handleCopyTimeslot}
					className={"btn-sm app-button text-uppercase"}
					disabled={loading === true}
				>
					<span className={"px-1"}>Spara</span>
					{loading === true && (
						<Spinner animation="border" size="sm" />
					)}
				</Button>
			</Modal.Footer>
		</Modal>
	);
};

export default BookSlotModal;
