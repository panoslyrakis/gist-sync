import React, { useState } from "react";
import Grid from "@mui/material/Grid";
import Box from "@mui/material/Box";
import MainTemplate from "./view/template.js";

const FilelistMetabox = () => {
  return (
    <>
      <Grid container spacing={3} >
        <Grid item xs={12}>
          <Box p={1} m={1}>
            <MainTemplate></MainTemplate>
          </Box>
        </Grid>
      </Grid>
    </>
  );
};

ReactDOM.render(
  <FilelistMetabox />,
  document.getElementById(gist_sync_settings.data.unique_id)
);
