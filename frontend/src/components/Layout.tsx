import { ReactNode } from 'react'
import Header from './Header'

interface LayoutProps {
  children: ReactNode
}

const Layout: React.FC<LayoutProps> = ({ children }) => {
  return (
    <div className="main">
      <Header />
      <div className="content">
        {children}
      </div>
    </div>
  )
}

export default Layout

