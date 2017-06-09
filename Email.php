<?php
// The message

Class Email
{
    public function sendMail($receiver, $subject, $message)
    {

// In case any of our lines are larger than 70 characters, we should use wordwrap()
        $message = wordwrap($message, 70, "\r\n");

// Send
        mail($receiver, $subject, $message);
    }
}
?>