
import Alert from '@mui/material/Alert';
import CheckIcon from '@mui/icons-material/Check';

import Box from "@mui/material/Box";

export default function Complete() {
    return (
        <Box
          sx={{
            marginTop: 8,
            display: "flex",
            flexDirection: "column",
            alignItems: "center",
          }}
        >
        <Alert icon={<CheckIcon fontSize="inherit" />} severity="success">all data is valid</Alert>
        </Box>
    );
}