import moment from "moment";
import TableThead from "./TableThead";
import Table from "react-bootstrap/Table";
import { useEffect, useState } from "react";
import CopyTimeSheet from "./CopyTimeSheet";
import WeekNavigation from "./WeekNavigation";
import FrontTimeSheet from "./FrontTimeSheet";
import AdminTimeSheet from "./AdminTimeSheet";
import {
	getNextTimeByInterval,
	getTimeFromTimeslotId,
} from "../../utils/helper";
import TableLoader from "./TableLoader";
import { fetchTimesheetData } from "../../utils/api";

const TableView = ({
	wpUserLoggedIn,
	activeTab,
	currentWeek,
	setCurrentWeek,
	categories,
	arena,
}) => {
	const [loading, setLoading] = useState(false);
	const [weekDays, setWeekDays] = useState([]);
	const [weekStart, setWeekStart] = useState("");
	const [weekEnd, setWeekEnd] = useState("");
	const [timeSlots, setTimeSlots] = useState([]);
	const [weekNumber, setWeekNumber] = useState("");
	const [timesheetData, setTimesheetData] = useState([]);
	const [activeArena, setArena] = useState([]);

	useEffect(() => {
		const getCurrentWeekNumber = () => {
			const firstDayOfYear = moment(currentWeek).startOf("year");
			const pastDaysOfYear = currentWeek.diff(firstDayOfYear, "days");
			const weekNumber = Math.ceil(
				(pastDaysOfYear + firstDayOfYear.day() + 1) / 7
			);
			setWeekNumber(weekNumber);
		};

		getCurrentWeekNumber();
	}, [currentWeek]);

	useEffect(() => {
		const getWeekDates = () => {
			const startOfWeek = moment(currentWeek).startOf("isoWeek");
			const endOfWeek = moment(currentWeek).endOf("isoWeek");
			const days = [];
			let firstLoop = true;

			for (
				let day = moment(startOfWeek);
				day <= endOfWeek;
				day = day.clone().add(1, "day")
			) {
				const formattedDate = day.format("YYYY-MM-DD");
				days.push(formattedDate);

				if (firstLoop) {
					setWeekStart(formattedDate);
					firstLoop = false;
				}

				if (day.isSame(endOfWeek, "day")) {
					setWeekEnd(formattedDate);
				}
			}

			setWeekDays(days);
		};

		getWeekDates();
	}, [currentWeek]);

	useEffect(() => {
		const generateTimeSlots = () => {
			if (activeTab > 0) {
				const category = arena.find((item) => item.id === activeTab);

				if (typeof category !== "undefined") {
					setArena(category);

					const slots = [];
					const startTime =
						category.start_time !== undefined &&
							category.start_time !== null &&
							category.start_time !== ""
							? category.start_time.replace(/^(\d):/, '0$1:')
							: "07:00";
					const endTime =
						category.end_time !== undefined &&
							category.end_time !== null &&
							category.end_time !== ""
							? category.end_time.replace(/^(\d):/, '0$1:')
							: "21:00";
					const intervalTime =
						category.interval_time !== undefined &&
							category.interval_time !== null &&
							category.interval_time !== ""
							? category.interval_time
							: "30";

					let currentTime = startTime;

					while (currentTime !== endTime) {
						const nextSlot = getNextTimeByInterval(
							currentTime,
							intervalTime
						);
						slots.push(`${currentTime}-${nextSlot}`);
						currentTime = nextSlot;
					}

					setTimeSlots(slots);
				}
			}
		};

		generateTimeSlots();
	}, [activeTab, arena]);

	const fetchTimesheetDataForWeek = async () => {
		try {
			setLoading(true);

			const startDate = moment(currentWeek)
				.startOf("isoWeek")
				.format("YYYY-MM-DD");
			const endDate = moment(currentWeek)
				.endOf("isoWeek")
				.format("YYYY-MM-DD");

			const data = await fetchTimesheetData(activeTab, startDate, endDate);
			setTimesheetData(data);
		} catch (error) {
			console.error("Error fetching timesheet data:", error);
		} finally {
			setLoading(false);
		}
	};

	const updateData = (updatedRecord) => {
		setTimesheetData((prevData) => {
			const recordIndex = prevData.findIndex(
				(item) => item.id === updatedRecord.id
			);

			if (recordIndex !== -1) {
				const updatedData = prevData.map((item) => {
					if (item.id === updatedRecord.id) {
						return { ...item, ...updatedRecord };
					}
					return item;
				});
				return updatedData;
			} else {
				return [...prevData, updatedRecord];
			}
		});
	};

	const refreshTimesheetData = (newRecords) => {
		if (Array.isArray(newRecords)) {
			newRecords.forEach((newRecord) => {
				updateData(newRecord);
			});
		} else if (typeof newRecords === "object" && newRecords !== null) {
			updateData(newRecords);
		} else {
			console.error("Invalid data format for newRecords");
		}
	};

	const getTimesheetForSlot = (day, slot) => {
		const matchingTimesheet = timesheetData.find((item) => {
			try {
				if (
					item.scheduled_date +
					" " +
					getTimeFromTimeslotId(item.timeslot_id) ===
					day + " " + slot
				) {
					return item;
				}
			} catch (error) {
				console.error("Error occurred:", error);
				return false;
			}
		});

		return matchingTimesheet;
	};

	useEffect(() => {
		fetchTimesheetDataForWeek(activeTab);
	}, [currentWeek, activeTab]);

	return (
		<>
			<WeekNavigation
				currentWeek={currentWeek}
				setCurrentWeek={setCurrentWeek}
			/>

			{wpUserLoggedIn && (
				<CopyTimeSheet
					activeTab={activeTab}
					currentWeek={currentWeek}
					weekNumber={weekNumber}
					weekStart={weekStart}
					weekEnd={weekEnd}
				/>
			)}

			<Table
				responsive
				className={"table table-bordered text-center"}
			>
				<TableThead weekNumber={weekNumber} weekDays={weekDays} />

				<tbody>
					{loading ? (
						<tr>
							<td colSpan={weekDays.length + 1} className="p-0">
								<TableLoader />
							</td>
						</tr>
					) : (
						timeSlots.map((slot, index) => (
							<tr key={index}>
								<td>{slot}</td>
								{weekDays.map((day, dayIndex) =>
									wpUserLoggedIn ? (
										<AdminTimeSheet
											key={`admin_${dayIndex}_${index}`}
											activeTab={activeTab}
											getTimesheetForSlot={
												getTimesheetForSlot
											}
											day={day}
											slot={slot}
											categories={categories}
											activeArena={activeArena}
											refreshTimesheetData={
												refreshTimesheetData
											}
										/>
									) : (
										<FrontTimeSheet
											key={`front_${dayIndex}_${index}`}
											getTimesheetForSlot={
												getTimesheetForSlot
											}
											day={day}
											slot={slot}
										/>
									)
								)}
							</tr>
						))
					)}
				</tbody>
			</Table>
		</>
	);
};

export default TableView;
