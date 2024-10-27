import Modal from "react-bootstrap/Modal";
import { useEffect, useState } from "react";
import Button from "react-bootstrap/Button";
import Spinner from "react-bootstrap/Spinner";
import { saveTimeslotComment } from "../../utils/api"; // Import the function from utils/api

const CommentModal = ({
	showTimeSlotCommentModal,
	handleCloseTimeSlotCommentModal,
	activeTab,
	day,
	slot,
	category,
	refreshTimesheetData,
	savedComment,
}) => {
	// State to manage the loading state while submitting
	const [loading, setLoading] = useState(false);

	// State to manage the value of the input field
	const [comment, setCommentValue] = useState("");

	// Split the time slot into start and end times
	const [startTime, endTime] = slot.split("-");

	// Format the date
	const formattedDate = day.replace(/-/g, "");

	// Combine the date and time
	const combinedValue =
		formattedDate + startTime.replace(":", "") + endTime.replace(":", "");

	// Function to handle input changes and update the state
	const handleInputChange = (event) => {
		setCommentValue(event.target.value);
	};

	// Effect to set savedComment as the default value for comment when the modal is displayed
	useEffect(() => {
		if (showTimeSlotCommentModal && savedComment !== undefined) {
			setCommentValue(savedComment);
		}
	}, [showTimeSlotCommentModal, savedComment]);

	// Function to handle saving the selected time slot
	const save = async () => {
		setLoading(true);

		const data = {
			arena_id: activeTab,
			timeslot_id: combinedValue,
			comment: comment,
		};

		try {
			const response = await saveTimeslotComment(data); // Use the function from utils/api
			if (response.status === 1) {
				refreshTimesheetData(response.data);
				handleCloseTimeSlotCommentModal();
			}
		} catch (error) {
			console.error("Error saving timeslot:", error);
		} finally {
			setLoading(false);
		}
	};

	return (
		<Modal
			show={showTimeSlotCommentModal}
			onHide={handleCloseTimeSlotCommentModal}
			size="sm"
			aria-labelledby="contained-modal-title-vcenter"
			centered
		>
			<Modal.Body>
				<input
					id={day + slot + "COMMENT"}
					type="text"
					className={"form-control"}
					value={comment}
					onChange={handleInputChange}
					maxLength={100}
				/>
			</Modal.Body>

			<Modal.Footer>
				<Button
					onClick={save}
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

export default CommentModal;
