import moment from "moment";
import { useEffect, useState } from "react";
import Modal from "react-bootstrap/Modal";
import Button from "react-bootstrap/Button";
import Spinner from "react-bootstrap/Spinner";
import { copyTimesheetData } from "../../utils/api";

const CopyTimeSheet = ({ activeTab, weekNumber, weekStart, weekEnd }) => {
	// State to manage the loading state while submitting
	const [loading, setLoading] = useState(false);

	// State for managing modal visibility and selected date
	const [show, setShow] = useState(false);
	const [selectedDate, setSelectedDate] = useState(moment(weekEnd));
	const [selectedWeek, setSelectedWeek] = useState("");

	// Function to close the modal
	const handleClose = () => setShow(false);

	// Function to open the modal
	const handleShow = () => setShow(true);

	// Function to handle date change in the <select> element
	const handleWeekChange = (e) => {
		setSelectedWeek(e.target.value);
	};

	// Function to get week numbers for the remaining year, skipping selected date's week
	const getRemainingYearWeeks = () => {
		const weeks = [];
		let current = selectedDate.clone().add(1, "week").startOf("isoWeek");

		while (current.isoWeekYear() === selectedDate.isoWeekYear()) {
			weeks.push(
				<option
					key={current.format("YYYY-MM-DD")}
					value={current.isoWeek()}
				>
					{current.isoWeek()}
				</option>
			);
			current.add(1, "week");
		}

		return weeks;
	};

	// Function to handle saving the selected time slot
	const copy = async () => {
		setLoading(true);

		const data = {
			begin: weekStart,
			arena_id: activeTab,
			copy_to_week: selectedWeek,
			copy_week_no: weekNumber,
		};

		try {
			const response = await copyTimesheetData(data); // Use the function from utils/api
			if (response.status === 1) {
				handleClose();
			}
		} catch (error) {
			// Handle errors
			console.error("Error saving timeslot:", error);
		} finally {
			setLoading(false);
		}
	};

	// Effect to set selectedDate after the initial render
	useEffect(() => {
		setSelectedDate(moment(weekEnd));
	}, [weekEnd]);

	// Render component
	return (
		<>
			<div
				className={
					"d-flex justify-content-center justify-content-md-end my-2"
				}
			>
				<Button
					variant="link"
					className={"app-button"}
					onClick={handleShow}
				>
					KOPIERA
				</Button>
			</div>

			<Modal show={show} onHide={handleClose}>
				<Modal.Header closeButton>
					<Modal.Title>KOPIERA VECKA {weekNumber}</Modal.Title>
				</Modal.Header>

				<Modal.Body>
					<select
						id={activeTab + weekNumber}
						className={"form-select"}
						onChange={handleWeekChange}
					>
						<option></option>
						{getRemainingYearWeeks()}
					</select>
				</Modal.Body>

				<Modal.Footer>
					<Button
						onClick={copy}
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
		</>
	);
};

export default CopyTimeSheet;
