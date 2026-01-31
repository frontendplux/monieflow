import { useState } from 'react'
import 'bootstrap/dist/css/bootstrap.min.css'
import 'bootstrap/dist/js/bootstrap.bundle.min.js';
import 'remixicon/fonts/remixicon.css'
import Login from './main/auth/login'
import './App.css'
import { Routes, Route } from "react-router-dom";
import ForgetPassword from './main/auth/forget-password'
import Signup from './main/auth/signup'
import CreatePage from './main/auth/create-page';
import Member, { Layout } from './main/member';

function App() {
  const [count, setCount] = useState(0)

  return (
    <Routes>
      <Route element={<Member  />} />
      <Route path="/" element={<Login />} />
      <Route path="/forget-password" element={<ForgetPassword />} />
      <Route path="/signup" element={<Signup />} />
      <Route path='/create-page' element={<CreatePage />} />
      <Route path='/member' element={<Member />} />
    </Routes>
  )
}
export default App
