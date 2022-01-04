import React, { useState, useContext } from "react";
import Box from "@mui/material/Box";
import List from "@mui/material/List";
import ListItem from "@mui/material/ListItem";
import ListItemButton from "@mui/material/ListItemButton";
import IconButton from "@mui/material/IconButton";
import ListItemText from "@mui/material/ListItemText";
import DeleteIcon from "@mui/icons-material/Delete";

import Button from "@mui/material/Button";

import Dialog from "@mui/material/Dialog";
import DialogActions from "@mui/material/DialogActions";
import DialogContent from "@mui/material/DialogContent";
import DialogContentText from "@mui/material/DialogContentText";
import DialogTitle from "@mui/material/DialogTitle";

import TextField from '@mui/material/TextField';

import { Context } from "./Provider";

const Fileslist = () => {
	const [deleteFileConfirmOpen, setDeleteFileConfirmOpen] =
		React.useState(false);
	const [fileNameToDelete, setFileNameToDelete] = React.useState("");

	const {
		setFileEditorState,
		files,
		setEditorFiles,
		setEditorDataFilename,
		setEditorDataFileContent,
		setPrevEditorDataFilename
	} = useContext(Context);

	const handleDeleteFile = (fileName) => {
		setFileNameToDelete(fileName);
		setDeleteFileConfirmOpen(true);
	};

	const handleFileDeleteConfirmClose = () => {
		setDeleteFileConfirmOpen(false);
	};

	const handleFileDeleteConfirm = () => {
		setDeleteFileConfirmOpen(false);

		let newFiles = files.filter((item) => {
			return item.fileName !== fileNameToDelete;
		});
		setEditorFiles(newFiles);
	};

	const handleEditFile = (fileName) => {
		let fileIndex = files.findIndex(
			(el) => el.fileName == fileName
		);
		let fileContent = files[ fileIndex ].fileContent;

		setPrevEditorDataFilename(fileName);
		setEditorDataFilename(fileName);
		setEditorDataFileContent(fileContent);
		setFileEditorState(true);
	};

	return (
		<>
			<Box p={1} m={1}>
				<List>
					{files.map((file) => {
						return (
							<ListItem
								secondaryAction={
									<IconButton
										edge="end"
										aria-label="delete"
										onClick={(e) => handleDeleteFile(file.fileName)}
									>
										<DeleteIcon />
									</IconButton>
								}
							>
								<ListItemButton
									aria-label={`edit file  ${file.fileName}`}
									onClick={(e) => handleEditFile(file.fileName)}
								>
									<ListItemText primary={file.fileName} />
								</ListItemButton>

								<input 
									type="hidden" name={"wpgist-filedata[" + file.fileName + "]"} 
									value={file.fileContent}
								/>
								
							</ListItem>
						);
					})}
				</List>
			</Box>

			<Dialog
				open={deleteFileConfirmOpen}
				onClose={handleFileDeleteConfirmClose}
				aria-labelledby="responsive-dialog-title"
			>
				<DialogTitle id="responsive-dialog-title">
					{"Are you sure you want to delete this file?"}
				</DialogTitle>
				<DialogContent>
					<DialogContentText>
						You are about to delete file <strong>{fileNameToDelete}</strong>. If
						you are sure click on the "Delete file" button
					</DialogContentText>
				</DialogContent>
				<DialogActions>
					<Button autoFocus onClick={handleFileDeleteConfirmClose}>
						Keep file
					</Button>
					<Button onClick={handleFileDeleteConfirm} autoFocus>
						Delete file
					</Button>
				</DialogActions>
			</Dialog>
		</>
	);
};

export default Fileslist;
