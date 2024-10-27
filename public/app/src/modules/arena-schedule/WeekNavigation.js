import moment from "moment";
import Button from "react-bootstrap/Button";
import { FontAwesomeIcon } from "@fortawesome/react-fontawesome";
import {
	faChevronLeft,
	faChevronRight,
} from "@fortawesome/free-solid-svg-icons";

const WeekNavigation = ({ currentWeek, setCurrentWeek }) => {
	const goToCurrentWeek = () => {
		setCurrentWeek(moment());
	};

	const goToPreviousWeek = () => {
		// Update 'currentWeek' state by subtracting 1 week from the current value
		setCurrentWeek(moment(currentWeek).subtract(1, "week"));
	};

	const goToNextWeek = () => {
		// Update 'currentWeek' state by adding 1 week to the current value
		setCurrentWeek(moment(currentWeek).add(1, "week"));
	};

	return (
		<div
			className={"calendar-navigation d-flex justify-content-center my-5"}
		>
			<Button
				variant="link"
				className={"arrow-button px-4"}
				onClick={goToPreviousWeek}
			>
				<FontAwesomeIcon icon={faChevronLeft} />
			</Button>

			<Button
				variant="link"
				className={"app-button"}
				onClick={() => goToCurrentWeek()}
			>
				DENNA VECKA
			</Button>

			<Button
				variant="link"
				className={"arrow-button px-4"}
				onClick={goToNextWeek}
			>
				<FontAwesomeIcon icon={faChevronRight} />
			</Button>
		</div>
	);
};

export default WeekNavigation;
