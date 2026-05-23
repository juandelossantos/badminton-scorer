import { Routes, Route } from 'react-router-dom'
import { ThemeProvider } from './context/ThemeContext'
import Home from './pages/Home'
import CreateMatch from './pages/CreateMatch'
import ShareURL from './pages/ShareURL'
import Match from './pages/Match'
import MatchTV from './pages/MatchTV'
import Celebration from './pages/Celebration'

function App() {
  return (
    <ThemeProvider>
      <Routes>
        <Route path="/" element={<Home />} />
        <Route path="/create" element={<CreateMatch />} />
        <Route path="/share" element={<ShareURL />} />
        <Route path="/match/:matchId" element={<Match />} />
        <Route path="/watch/:matchId" element={<MatchTV />} />
        <Route path="/match/:matchId/celebration" element={<Celebration />} />
      </Routes>
    </ThemeProvider>
  )
}

export default App
