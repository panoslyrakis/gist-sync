import React, { useState, useEffect } from "react";
import PropTypes from "prop-types";
import { useTheme } from "@mui/material/styles";
import Box from "@mui/material/Box";
import Table from "@mui/material/Table";
import TableBody from "@mui/material/TableBody";
import TableCell from "@mui/material/TableCell";
import TableContainer from "@mui/material/TableContainer";
import TableFooter from "@mui/material/TableFooter";
import TablePagination from "@mui/material/TablePagination";
import TableRow from "@mui/material/TableRow";
import Paper from "@mui/material/Paper";
import Grid from "@mui/material/Grid";
import IconButton from "@mui/material/IconButton";
import Button from "@mui/material/Button";
import SaveIcon from "@mui/icons-material/Save";
import TextField from "@mui/material/TextField";
import Switch from "@mui/material/Switch";
import FormGroup from "@mui/material/FormGroup";
import FormControlLabel from "@mui/material/FormControlLabel";
import Checkbox from "@mui/material/Checkbox";
import axios from "axios";
import Alert from "@mui/material/Alert";

const { __ } = wp.i18n;

export default function SettingsTable() {
	const default_settings = JSON.parse( gist_sync_settings.data.settings );
	const globalTokenLabel = {
		inputProps: {
			"aria-label": __("Switch to global token or not", "gist-sync"),
		},
	};
	const userRoles = JSON.parse(gist_sync_settings.data.user_roles);
	const [username, setUsername] = React.useState("");
	const [globalTokenStatus, setGlobalTokenStatus] = React.useState(false);
	const [globalToken, setGlobalToken] = React.useState("");
	const [userRolesAllowed, setUserRolesAllowed] = React.useState(new Object);
	const [settingsSaveNotification, setSettingsSaveNotification] = React.useState( {show:false, severity: '', message:''} );

	const SettingsNotification = ( props ) => (
		<Box pt={2} pb={2}><Alert onClose={() => {resetSettingsNotification()}} severity={props.severity}>{props.message}</Alert></Box>
	);

	const resetSettingsNotification = () => {
		setSettingsSaveNotification({show:false, severity: '', message:''})
	}

	const createTableRowData = ( name, optionDescription, optionValue ) => {
		return { name, optionDescription, optionValue };
	}

	// Set up initial values.
	useEffect(() => {
		setUsername( default_settings.username );
		setGlobalTokenStatus( default_settings.globaltokenstatus );
		setGlobalToken( default_settings.globaltoken );
		setUserRolesAllowed( JSON.parse( default_settings.userrolesallowed ) );
	}, default_settings); // Run only when default_settings changess

	/*
	const UserRolesList = () => (
		<FormGroup>
			{userRoles.map((role) => (
				<FormControlLabel
					control={
						<Switch
							checked={userRolesAllowed[role.name]}
							onChange={handleUserRolesChange}
							value={role.name}
						/>
					}
					size="small"
					label={role.label}
				/>
			))}
		</FormGroup>
	);
	*/

	const UserRolesList = () => (
		<FormGroup>
			{userRoles.map((role) => (
				<FormControlLabel
				control={
					<input
						type="checkbox"
						defaultChecked={userRolesAllowed[role.name]} 
						onChange={handleUserRolesChange}
						value={role.name}
					/>
				}
				size="small"
				label={role.label}
			/>
		))}
		</FormGroup>
	);

	const Api = axios.create({
		baseURL: gist_sync_settings.data.rest_url,
		headers: {
			"content-type": "application/json",
			"X-WP-Nonce": gist_sync_settings.data.nonce,
		},
	});


	const __handleUserRolesChange = (e, checked) => {
		let allowedRoles = userRolesAllowed
		allowedRoles[ e.target.value ] = checked;

		setUserRolesAllowed(allowedRoles);
	};

	const handleUserRolesChange = (e) => {
		let allowedRoles = userRolesAllowed
		allowedRoles[ e.target.value ] = e.target.checked;

		setUserRolesAllowed(allowedRoles);
	};

	const handleSave = (event) => {
		const data = {
			action: 'save',
			username: username,
			globalTokenStatus: globalTokenStatus,
			globalToken: globalToken,
			userRolesAllowed: JSON.stringify( Object.assign({}, userRolesAllowed) ),
		};

		Api.post(gist_sync_settings.data.rest_namespace, data).then(function (
			response
		) {
			
			resetSettingsNotification();

			if ( 200 == response.data.status_code) {
				setSettingsSaveNotification(
					{
						show: true,
						severity:'success',
						message: __('Settings saved', 'gist-sync' )
					}
				);

				setTimeout(resetSettingsNotification, 10000);
			} else {
				setSettingsSaveNotification(
					{
						show: true,
						severity:'error',
						message: __('Something went wrong', 'gist-sync' )
					}
				);
			}
		});
	};

	const rows = [
		createTableRowData(
			__("Github username", "gist-sync"),
			__("The username of your github account", "gist-sync"),
			<FormGroup>
				<TextField
					onChange={(e) => {
						setUsername(e.target.value);
					}}
					value={username}
				/>
			</FormGroup>
		),
		createTableRowData(
			__("Use global token", "gist-sync"),
			__(
				"Use the same token for all users or set a token per user",
				"gist-sync"
			),
			<FormGroup>
				<Switch
					{...globalTokenLabel}
					defaultChecked
					checked={globalTokenStatus}
					onClick={(e) => {
						setGlobalTokenStatus(!globalTokenStatus);
					}}
				/>
			</FormGroup>
		),
		createTableRowData(
			__("Global token", "gist-sync"),
			__("All users will use the same token", "gist-sync"),
			<FormGroup>
				<TextField
					onChange={(e) => {
						setGlobalToken(e.target.value);
					}}
					value={globalToken}
				/>
			</FormGroup>
		),
		createTableRowData(
			__("User roles", "gist-sync"),
			__("Choose what user roles will be able to edit gists.", "gist-sync"),
			<UserRolesList />
		),
	];

	return (
		<TableContainer component={Paper}>
			<Table sx={{ minWidth: 400 }} aria-label="custom pagination table">
				<TableBody>
					{rows.map((row) => (
						<TableRow key={row.name}>
							<TableCell
								component="th"
								scope="row"
								style={{ verticalAlign: "top" }}
							>
								<strong>{row.name}</strong>
							</TableCell>
							<TableCell align="left" style={{ verticalAlign: "top" }}>
								{row.optionDescription}
							</TableCell>
							<TableCell align="right" style={{ verticalAlign: "top" }}>
								{row.optionValue}
							</TableCell>
						</TableRow>
					))}
				</TableBody>
				<TableFooter>
					<TableRow>
						<TableCell colSpan={3}>
							<Grid>
								{
								settingsSaveNotification.show ? 
								<SettingsNotification 
									severity={settingsSaveNotification.severity} 
									message={settingsSaveNotification.message} 
								/> : 
								null
								}
							</Grid>
							<Grid container justifyContent="flex-end">
								<Button
									variant="contained"
									startIcon={<SaveIcon />}
									onClick={handleSave}
								>
									{__('Save', 'gist-sync')}
								</Button>
							</Grid>
						</TableCell>
					</TableRow>
				</TableFooter>
			</Table>
		</TableContainer>
	);
}
