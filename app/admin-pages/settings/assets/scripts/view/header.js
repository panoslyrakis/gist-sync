import React from 'react';
import Typography from '@mui/material/Typography';

const { __ } = wp.i18n;

const Header = () => {

		return (
			<>
				<Typography variant="h1">{gist_sync_settings.labels.page_title}</Typography>
				<Typography>{__( 'Configure options to sync with your gists', 'gist-sync' )}</Typography>
			</>
		);
 }

export default Header