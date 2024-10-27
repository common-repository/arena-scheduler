import { useState } from "react";
import Modal from "react-bootstrap/Modal";
import Button from "react-bootstrap/Button";
import { faInfo } from "@fortawesome/free-solid-svg-icons";
import { FontAwesomeIcon } from "@fortawesome/react-fontawesome";

const FrontTimeSheet = ({ getTimesheetForSlot, day, slot }) => {
	const [show, setShow] = useState(false);

	const handleClose = () => setShow(false);
	const handleShow = () => setShow(true);

	return (
		<td
			style={
				getTimesheetForSlot(day, slot)
					? { color: getTimesheetForSlot(day, slot).text_color, backgroundColor: getTimesheetForSlot(day, slot).color, border: '1px solid #cdcdcd' }
					: {}
			}
		>
			{getTimesheetForSlot(day, slot) && (
				<div>
					{getTimesheetForSlot(day, slot).comment ? (
						<span>
							<span className={"front-comment"}>
								{getTimesheetForSlot(day, slot).comment}
							</span>

							<span className={"view-comment"}>
								<Button
									variant="link"
									className={"p-0"}
									onClick={handleShow}
								>
									<FontAwesomeIcon icon={faInfo} style={{ color: getTimesheetForSlot(day, slot).text_color }} />
								</Button>

								<Modal
									size="md"
									aria-labelledby="contained-modal-title-vcenter"
									centered
									show={show}
									onHide={handleClose}
								>
									<Modal.Body>
										{getTimesheetForSlot(day, slot).comment}
									</Modal.Body>
									<Modal.Footer className={"p-1"}>
										<Button
											onClick={handleClose}
											className={"btn-sm app-button"}
										>
											Stänga
										</Button>
									</Modal.Footer>
								</Modal>
							</span>
						</span>
					) : (
						<span>&nbsp;</span>
					)}
				</div>
			)}
		</td>
	);
};

export default FrontTimeSheet;
