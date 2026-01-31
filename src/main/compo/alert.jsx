import React from "react";
import { Button } from "react-native";
import Toast from "react-native-toast-message";

export default function MyComponent() {
  const showToast = () => {
    Toast.show({
      type: "success",          // success, error, info
      text1: "Success!",
      text2: "Your action was completed.",
      visibilityTime: 3000,     // auto-hide after 3 seconds
      position: "top",          // top or bottom
      topOffset: 50,            // distance from top
    });
  };

  return (
    <Button title="Show Toast" onPress={showToast} />
  );
}