import Lottie from 'lottie-react';
import infiniteLoop from 'assets/img/animated-icons/infinite-loop.json';

const LoaderInfiniteLoop = () => {
    return <center><Lottie animationData={infiniteLoop} loop={true} style={{height: '130px', width: '130px'}} /></center>;
}

LoaderInfiniteLoop.propTypes = {};

export default LoaderInfiniteLoop;