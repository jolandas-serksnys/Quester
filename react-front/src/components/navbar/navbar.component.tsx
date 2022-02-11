import { css } from '@emotion/css';
import React, { useContext } from 'react';
import { Link } from 'react-router-dom';
import { UserContext } from '../../contexts/user.context';

const Navbar: React.FC = () => {
    const {isAuthenticated, user, handleLogout } = useContext(UserContext);

    return (
        <div>
            <nav className='container'>
                <ul className={css`
                    list-style: none;
                    padding: 1rem 0;
                    display: flex;
                    gap: 0.5rem;
                    align-items: center;

                    li a,
                    li button {
                        padding: 1rem 1.5rem;
                        border-radius: 0.5rem;
                        font-weight: bold;
                        display: inline-block;
                        border: 1px solid #efefef;
                        transition: all 0.1s ease-in-out;

                        &:hover {
                            background: #efefef;
                        }

                        &:active {
                            transition: none;
                            border-color: #dddddd;
                            background: #dddddd;
                        }
                    }
                `}>
                    <li>
                        <Link to='/'>
                            Home
                        </Link>
                    </li>
                    <UserContext.Consumer>
                        {value => (
                            <>
                                <li className={css`
                                    margin-left: auto;
                                `}/>
                                {user && user.access_token != null && <>
                                <li className={css`
                                    padding: 0 1rem;
                                `}>Welcome back, {user.email}</li>
                                <li>
                                    <Link to='/profile'>
                                        Profile
                                    </Link>    
                                </li>
                                <li>
                                    <button onClick={handleLogout}>
                                        Logout
                                    </button> 
                                </li>
                                </>}
                                {!user || user.access_token == null && 
                                <li>
                                    <Link to='/login'>
                                        Login
                                    </Link>    
                                </li>}
                            </>
                        )}
                    </UserContext.Consumer>
                </ul>
            </nav>
        </div>
    );
}

export default Navbar;