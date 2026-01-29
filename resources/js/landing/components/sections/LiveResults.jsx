import { useState, useEffect } from 'react';
import { motion, AnimatePresence } from 'framer-motion';
import { MdHowToVote, MdTimer, MdPerson, MdLeaderboard, MdPlayCircle } from 'react-icons/md';
import { HiQuestionMarkCircle, HiTrendingUp, HiClock, HiStatusOnline } from 'react-icons/hi';

export default function LiveResults() {
    const [elections, setElections] = useState([]);
    const [loading, setLoading] = useState(true);
    const [error, setError] = useState(null);
    const [lastUpdate, setLastUpdate] = useState(null);

    // Fetch live results
    const fetchResults = async () => {
        try {
            const response = await fetch('/api/live-results');
            const data = await response.json();
            
            if (data.success) {
                setElections(data.elections);
                setLastUpdate(new Date());
                setError(null);
            } else {
                setError('Failed to load results');
            }
        } catch (err) {
            console.error('Error fetching live results:', err);
            setError('Unable to connect to server');
        } finally {
            setLoading(false);
        }
    };

    // Initial fetch and polling every 5 seconds for real-time updates (faster for ongoing elections)
    useEffect(() => {
        fetchResults();
        const interval = setInterval(fetchResults, 5000);
        return () => clearInterval(interval);
    }, []);

    // Don't render if no elections
    if (!loading && elections.length === 0) {
        return null;
    }

    // Separate ongoing and completed elections
    const ongoingElections = elections.filter(e => e.status === 'ongoing');
    const completedElections = elections.filter(e => e.status === 'completed');

    return (
        <section id="live-results" className="py-16 sm:py-20 lg:py-24 relative overflow-hidden" style={{
            background: 'linear-gradient(180deg, #0c1220 0%, #1a2332 40%, #243447 100%)'
        }}>
            {/* Diagonal Lines Pattern */}
            <div className="absolute inset-0 opacity-[0.03]" style={{
                backgroundImage: `repeating-linear-gradient(
                    45deg,
                    transparent,
                    transparent 10px,
                    rgba(255,255,255,0.1) 10px,
                    rgba(255,255,255,0.1) 11px
                )`,
            }} />
            
            {/* Top Gold Accent Line */}
            <div className="absolute top-0 left-0 right-0 h-1 bg-gradient-to-r from-transparent via-gov-gold-500 to-transparent" />
            
            {/* Ambient Glow Effects */}
            <div className="absolute inset-0 overflow-hidden pointer-events-none">
                {/* Top right warm glow */}
                <div className="absolute -top-32 -right-32 w-96 h-96 bg-gov-gold-500/15 rounded-full blur-[120px]" />
                
                {/* Bottom left cool glow */}
                <div className="absolute -bottom-32 -left-32 w-[500px] h-[500px] bg-slate-400/10 rounded-full blur-[150px]" />
                
                {/* Center accent */}
                <motion.div
                    animate={{ 
                        opacity: [0.05, 0.15, 0.05],
                    }}
                    transition={{ duration: 3, repeat: Infinity, ease: 'easeInOut' }}
                    className="absolute top-1/2 left-1/2 -translate-x-1/2 -translate-y-1/2 w-[800px] h-[400px] bg-gov-gold-400/5 rounded-full blur-[100px]"
                />
            </div>
            
            {/* Bottom border accent */}
            <div className="absolute bottom-0 left-0 right-0 h-px bg-gradient-to-r from-transparent via-white/10 to-transparent" />

            <div className="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 relative z-10">
                {/* Section Header */}
                <div className="text-center mb-10 sm:mb-12 lg:mb-16">
                    <motion.div
                        initial={{ opacity: 0, y: 20 }}
                        whileInView={{ opacity: 1, y: 0 }}
                        viewport={{ once: true }}
                        transition={{ duration: 0.5 }}
                        className="inline-flex items-center gap-2 bg-white/10 backdrop-blur-sm px-4 py-2 rounded-full mb-4"
                    >
                        <span className="w-2 h-2 bg-red-500 rounded-full animate-pulse" />
                        <span className="text-white/90 text-sm font-medium">
                            {ongoingElections.length > 0 ? 'Live Voting in Progress' : 'Recent Election Results'}
                        </span>
                    </motion.div>
                    
                    <motion.h2
                        initial={{ opacity: 0, y: 20 }}
                        whileInView={{ opacity: 1, y: 0 }}
                        viewport={{ once: true }}
                        transition={{ duration: 0.5, delay: 0.1 }}
                        className="text-2xl sm:text-3xl md:text-4xl lg:text-5xl font-bold text-white mb-4"
                    >
                        {ongoingElections.length > 0 ? 'Live Election Results' : 'Recent Election Results'}
                    </motion.h2>
                    
                    <motion.p
                        initial={{ opacity: 0, y: 20 }}
                        whileInView={{ opacity: 1, y: 0 }}
                        viewport={{ once: true }}
                        transition={{ duration: 0.5, delay: 0.2 }}
                        className="text-sm sm:text-base lg:text-lg text-white/70 max-w-2xl mx-auto"
                    >
                        {ongoingElections.length > 0 
                            ? 'Watch real-time vote counts as they come in! Candidate identities are hidden to ensure fair voting.'
                            : 'Transparent voting results displayed anonymously. Results are available for 24 hours after election ends.'}
                    </motion.p>
                </div>

                {/* Loading State */}
                {loading && (
                    <div className="flex flex-col items-center justify-center py-12">
                        <div className="w-16 h-16 border-4 border-white/20 border-t-gov-gold-400 rounded-full animate-spin mb-4" />
                        <p className="text-white/70">Loading live results...</p>
                    </div>
                )}

                {/* Error State */}
                {error && !loading && (
                    <div className="text-center py-12">
                        <div className="w-16 h-16 bg-red-500/20 rounded-full flex items-center justify-center mx-auto mb-4">
                            <HiQuestionMarkCircle className="w-8 h-8 text-red-400" />
                        </div>
                        <p className="text-white/70">{error}</p>
                    </div>
                )}

                {/* Elections Grid */}
                {!loading && !error && elections.length > 0 && (
                    <div className="space-y-8">
                        {/* Ongoing Elections First */}
                        {ongoingElections.map((election, electionIndex) => (
                            <motion.div
                                key={election.id}
                                initial={{ opacity: 0, y: 30 }}
                                whileInView={{ opacity: 1, y: 0 }}
                                viewport={{ once: true }}
                                transition={{ duration: 0.5, delay: electionIndex * 0.1 }}
                                className="bg-white/10 backdrop-blur-md rounded-2xl p-5 sm:p-6 lg:p-8 border-2 border-gov-gold-400/50 shadow-lg shadow-gov-gold-500/20"
                            >
                                {/* Election Header */}
                                <div className="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 mb-6 pb-4 border-b border-white/10">
                                    <div>
                                        <div className="flex items-center gap-2 mb-2">
                                            <span className="inline-flex items-center gap-1.5 bg-green-500/20 border border-green-500/30 text-green-400 text-xs font-semibold px-2.5 py-1 rounded-full">
                                                <HiStatusOnline className="w-3 h-3 animate-pulse" />
                                                LIVE NOW
                                            </span>
                                        </div>
                                        <h3 className="text-xl sm:text-2xl font-bold text-white mb-1">
                                            {election.election_name}
                                        </h3>
                                        {election.organization && (
                                            <p className="text-white/60 text-sm">{election.organization}</p>
                                        )}
                                        {election.started_at && (
                                            <p className="text-white/50 text-xs mt-1">
                                                Started: {election.started_at}
                                            </p>
                                        )}
                                    </div>
                                    
                                    {/* Time Until End */}
                                    <ElectionCountdown 
                                        type="ongoing"
                                        timeRemaining={election.time_remaining}
                                        endsAt={election.ends_at}
                                    />
                                </div>

                                {/* Stats Bar */}
                                <div className="flex flex-wrap gap-4 mb-6">
                                    <div className="flex items-center gap-2 bg-white/5 rounded-lg px-3 py-2">
                                        <MdPerson className="w-4 h-4 text-gov-gold-400" />
                                        <span className="text-white/80 text-sm">
                                            <span className="font-semibold text-white">{election.total_voters}</span> votes cast
                                        </span>
                                    </div>
                                    <div className="flex items-center gap-2 bg-white/5 rounded-lg px-3 py-2">
                                        <MdLeaderboard className="w-4 h-4 text-gov-gold-400" />
                                        <span className="text-white/80 text-sm">
                                            <span className="font-semibold text-white">{election.positions.length}</span> positions
                                        </span>
                                    </div>
                                </div>

                                {/* Positions and Candidates */}
                                <div className="space-y-6">
                                    {election.positions.length > 0 ? (
                                        election.positions.map((position, posIndex) => (
                                            <div key={position.position_id} className="bg-white/5 rounded-xl p-4 sm:p-5">
                                                <h4 className="text-lg sm:text-xl font-semibold text-white mb-4 flex items-center gap-2">
                                                    <MdHowToVote className="w-5 h-5 text-gov-gold-400" />
                                                    {position.position_name}
                                                </h4>
                                                
                                                {/* Candidates Grid */}
                                                <div className="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-4 lg:grid-cols-5 gap-3 sm:gap-4">
                                                    {position.candidates.map((candidate, candIndex) => (
                                                        <CandidateCard 
                                                            key={candidate.id}
                                                            candidate={candidate}
                                                            totalVotes={position.total_votes}
                                                            rank={candIndex + 1}
                                                            isLeader={candIndex === 0 && position.total_votes > 0}
                                                            isLive={true}
                                                        />
                                                    ))}
                                                </div>
                                            </div>
                                        ))
                                    ) : (
                                        <div className="text-center py-8 text-white/50">
                                            <MdHowToVote className="w-12 h-12 mx-auto mb-3 opacity-50" />
                                            <p>No candidates registered yet</p>
                                        </div>
                                    )}
                                </div>
                            </motion.div>
                        ))}

                        {/* Completed Elections */}
                        {completedElections.length > 0 && ongoingElections.length > 0 && (
                            <div className="flex items-center gap-4 mt-8">
                                <div className="flex-1 h-px bg-white/20"></div>
                                <span className="text-white/50 text-sm font-medium">Recently Completed</span>
                                <div className="flex-1 h-px bg-white/20"></div>
                            </div>
                        )}

                        {completedElections.map((election, electionIndex) => (
                            <motion.div
                                key={election.id}
                                initial={{ opacity: 0, y: 30 }}
                                whileInView={{ opacity: 1, y: 0 }}
                                viewport={{ once: true }}
                                transition={{ duration: 0.5, delay: electionIndex * 0.1 }}
                                className="bg-white/10 backdrop-blur-md rounded-2xl p-5 sm:p-6 lg:p-8 border border-gov-gold-500/30 shadow-lg shadow-gov-gold-500/10"
                            >
                                {/* Election Header */}
                                <div className="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 mb-6 pb-4 border-b border-white/10">
                                    <div>
                                        <div className="flex items-center gap-2 mb-2">
                                            <span className="inline-flex items-center gap-1.5 bg-gov-gold-500/20 border border-gov-gold-500/40 text-gov-gold-400 text-xs font-semibold px-2.5 py-1 rounded-full animate-pulse">
                                                <span className="text-sm">🏆</span>
                                                RESULTS REVEALED
                                            </span>
                                        </div>
                                        <h3 className="text-xl sm:text-2xl font-bold text-white mb-1">
                                            {election.election_name}
                                        </h3>
                                        {election.organization && (
                                            <p className="text-white/60 text-sm">{election.organization}</p>
                                        )}
                                        <p className="text-white/50 text-xs mt-1">
                                            Election ended: {election.ended_at}
                                        </p>
                                    </div>
                                    
                                    {/* Expiry Countdown */}
                                    <ElectionCountdown 
                                        type="completed"
                                        timeRemaining={election.time_remaining}
                                        expiresAt={election.expires_at}
                                    />
                                </div>

                                {/* Stats Bar */}
                                <div className="flex flex-wrap gap-4 mb-6">
                                    <div className="flex items-center gap-2 bg-white/5 rounded-lg px-3 py-2">
                                        <MdPerson className="w-4 h-4 text-gov-gold-400" />
                                        <span className="text-white/80 text-sm">
                                            <span className="font-semibold text-white">{election.total_voters}</span> total voters
                                        </span>
                                    </div>
                                    <div className="flex items-center gap-2 bg-white/5 rounded-lg px-3 py-2">
                                        <MdLeaderboard className="w-4 h-4 text-gov-gold-400" />
                                        <span className="text-white/80 text-sm">
                                            <span className="font-semibold text-white">{election.positions.length}</span> positions
                                        </span>
                                    </div>
                                </div>

                                {/* Positions and Candidates */}
                                <div className="space-y-6">
                                    {election.positions.length > 0 ? (
                                        election.positions.map((position, posIndex) => (
                                            <div key={position.position_id} className="bg-white/5 rounded-xl p-4 sm:p-5">
                                                <h4 className="text-lg sm:text-xl font-semibold text-white mb-4 flex items-center gap-2">
                                                    <MdHowToVote className="w-5 h-5 text-gov-gold-400" />
                                                    {position.position_name}
                                                </h4>
                                                
                                                {/* Candidates Grid */}
                                                <div className="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-4 lg:grid-cols-5 gap-3 sm:gap-4">
                                                    {position.candidates.map((candidate, candIndex) => (
                                                        <CandidateCard 
                                                            key={candidate.id}
                                                            candidate={candidate}
                                                            totalVotes={position.total_votes}
                                                            rank={candIndex + 1}
                                                            isLeader={candIndex === 0 && position.total_votes > 0}
                                                            isLive={false}
                                                        />
                                                    ))}
                                                </div>
                                            </div>
                                        ))
                                    ) : (
                                        <div className="text-center py-8 text-white/50">
                                            <MdHowToVote className="w-12 h-12 mx-auto mb-3 opacity-50" />
                                            <p>No candidates were registered</p>
                                        </div>
                                    )}
                                </div>
                            </motion.div>
                        ))}

                        {/* Last Update Indicator */}
                        {lastUpdate && (
                            <div className="text-center">
                                <p className="text-white/40 text-xs">
                                    Last updated: {lastUpdate.toLocaleTimeString()} • Auto-refreshes every 5 seconds
                                </p>
                            </div>
                        )}
                    </div>
                )}
            </div>
        </section>
    );
}

// Candidate Card Component - Shows anonymous or revealed based on election status
function CandidateCard({ candidate, totalVotes, rank, isLeader, isLive }) {
    const percentage = totalVotes > 0 ? ((candidate.votes_count / totalVotes) * 100).toFixed(1) : 0;
    const isAnonymous = candidate.is_anonymous;
    
    return (
        <motion.div
            initial={{ opacity: 0, scale: 0.9 }}
            animate={{ opacity: 1, scale: 1 }}
            layout
            className={`
                relative bg-white/10 rounded-xl p-3 sm:p-4 text-center
                ${isLeader ? 'ring-2 ring-gov-gold-400 bg-gov-gold-500/10' : ''}
                transition-all duration-300 hover:bg-white/15
            `}
        >
            {/* Winner/Leader Badge */}
            {isLeader && (
                <motion.div 
                    initial={{ scale: 0 }}
                    animate={{ scale: 1 }}
                    className="absolute -top-2 -right-2 w-7 h-7 bg-gov-gold-500 rounded-full flex items-center justify-center shadow-lg z-10"
                >
                    {isAnonymous ? (
                        <HiTrendingUp className="w-4 h-4 text-gov-green-900" />
                    ) : (
                        <span className="text-gov-green-900 text-xs font-bold">1st</span>
                    )}
                </motion.div>
            )}
            
            {/* Live indicator for ongoing elections */}
            {isLive && (
                <div className="absolute -top-1 -left-1 w-3 h-3 bg-green-500 rounded-full animate-pulse shadow-lg shadow-green-500/50" />
            )}
            
            {/* Photo - Anonymous or Revealed */}
            <AnimatePresence mode="wait">
                {isAnonymous ? (
                    <motion.div
                        key="anonymous"
                        initial={{ opacity: 0, rotateY: 90 }}
                        animate={{ opacity: 1, rotateY: 0 }}
                        exit={{ opacity: 0, rotateY: -90 }}
                        className="w-16 h-16 sm:w-20 sm:h-20 mx-auto mb-3 bg-gradient-to-br from-gray-600 to-gray-700 rounded-full flex items-center justify-center shadow-lg relative overflow-hidden"
                    >
                        <HiQuestionMarkCircle className="w-10 h-10 sm:w-12 sm:h-12 text-white/70" />
                        {isLive && (
                            <div className="absolute inset-0 bg-gradient-to-t from-gov-gold-500/20 to-transparent animate-pulse" />
                        )}
                    </motion.div>
                ) : (
                    <motion.div
                        key="revealed"
                        initial={{ opacity: 0, rotateY: 90, scale: 0.8 }}
                        animate={{ opacity: 1, rotateY: 0, scale: 1 }}
                        transition={{ duration: 0.6, ease: 'easeOut' }}
                        className={`w-16 h-16 sm:w-20 sm:h-20 mx-auto mb-3 rounded-full shadow-lg overflow-hidden ${isLeader ? 'ring-4 ring-gov-gold-400' : 'ring-2 ring-white/20'}`}
                    >
                        {candidate.photo ? (
                            <img 
                                src={candidate.photo} 
                                alt={candidate.name}
                                className="w-full h-full object-cover"
                                onError={(e) => {
                                    e.target.style.display = 'none';
                                    e.target.nextSibling.style.display = 'flex';
                                }}
                            />
                        ) : null}
                        <div 
                            className={`w-full h-full bg-gradient-to-br from-gov-green-600 to-gov-green-800 items-center justify-center ${candidate.photo ? 'hidden' : 'flex'}`}
                        >
                            <span className="text-2xl font-bold text-white">
                                {candidate.name?.charAt(0)?.toUpperCase() || '?'}
                            </span>
                        </div>
                    </motion.div>
                )}
            </AnimatePresence>
            
            {/* Name - Anonymous or Revealed */}
            <AnimatePresence mode="wait">
                <motion.h5
                    key={isAnonymous ? 'anon-name' : 'real-name'}
                    initial={{ opacity: 0, y: 10 }}
                    animate={{ opacity: 1, y: 0 }}
                    exit={{ opacity: 0, y: -10 }}
                    className={`font-bold text-white text-sm sm:text-base mb-1 truncate ${!isAnonymous && isLeader ? 'text-gov-gold-400' : ''}`}
                    title={candidate.name}
                >
                    {candidate.name}
                </motion.h5>
            </AnimatePresence>
            
            {/* Vote Count */}
            <div className="mt-2">
                <motion.p 
                    key={candidate.votes_count}
                    initial={{ scale: 1.1 }}
                    animate={{ scale: 1 }}
                    className={`text-xl sm:text-2xl font-bold ${isLeader && !isAnonymous ? 'text-gov-gold-300' : 'text-gov-gold-400'}`}
                >
                    {candidate.votes_count.toLocaleString()}
                </motion.p>
                <p className="text-white/50 text-xs">{isAnonymous ? 'votes' : 'final votes'}</p>
            </div>
            
            {/* Progress Bar */}
            <div className="mt-3 h-2 bg-white/10 rounded-full overflow-hidden">
                <motion.div
                    initial={{ width: 0 }}
                    animate={{ width: `${percentage}%` }}
                    transition={{ duration: 0.8, ease: 'easeOut' }}
                    className={`h-full rounded-full ${isLeader ? 'bg-gradient-to-r from-gov-gold-400 to-gov-gold-500' : 'bg-white/40'}`}
                />
            </div>
            <p className="text-white/60 text-xs mt-1">{percentage}%</p>
            
            {/* Rank Badge for revealed candidates */}
            {!isAnonymous && rank <= 3 && (
                <motion.div
                    initial={{ opacity: 0, scale: 0 }}
                    animate={{ opacity: 1, scale: 1 }}
                    transition={{ delay: 0.3 }}
                    className={`absolute -bottom-1 left-1/2 -translate-x-1/2 px-2 py-0.5 rounded-full text-xs font-bold ${
                        rank === 1 ? 'bg-gov-gold-500 text-gov-green-900' :
                        rank === 2 ? 'bg-gray-300 text-gray-800' :
                        'bg-amber-600 text-white'
                    }`}
                >
                    {rank === 1 ? '🏆 Winner' : rank === 2 ? '2nd' : '3rd'}
                </motion.div>
            )}
        </motion.div>
    );
}

// Election Countdown Component (for both ongoing and completed)
function ElectionCountdown({ type, timeRemaining, endsAt, expiresAt }) {
    const [time, setTime] = useState(timeRemaining || { hours: 0, minutes: 0, seconds: 0 });
    
    useEffect(() => {
        if (!timeRemaining) return;
        setTime(timeRemaining);
    }, [timeRemaining]);
    
    useEffect(() => {
        const interval = setInterval(() => {
            setTime(prev => {
                let { hours, minutes, seconds } = prev;
                
                if (seconds > 0) {
                    seconds--;
                } else if (minutes > 0) {
                    minutes--;
                    seconds = 59;
                } else if (hours > 0) {
                    hours--;
                    minutes = 59;
                    seconds = 59;
                }
                
                return { hours, minutes, seconds };
            });
        }, 1000);
        
        return () => clearInterval(interval);
    }, []);
    
    const formatTime = (val) => String(val).padStart(2, '0');
    
    if (type === 'ongoing') {
        return (
            <div className="bg-green-500/20 border border-green-500/30 rounded-lg px-4 py-2 flex items-center gap-2">
                <HiClock className="w-4 h-4 text-green-400 animate-pulse" />
                <div className="text-center">
                    <p className="text-green-400 text-xs uppercase tracking-wide mb-0.5">Voting ends in</p>
                    <p className="text-white font-mono font-bold text-sm">
                        {formatTime(time.hours)}:{formatTime(time.minutes)}:{formatTime(time.seconds)}
                    </p>
                </div>
            </div>
        );
    }
    
    return (
        <div className="bg-red-500/20 border border-red-500/30 rounded-lg px-4 py-2 flex items-center gap-2">
            <HiClock className="w-4 h-4 text-red-400" />
            <div className="text-center">
                <p className="text-red-400 text-xs uppercase tracking-wide mb-0.5">Results expire in</p>
                <p className="text-white font-mono font-bold text-sm">
                    {formatTime(time.hours)}:{formatTime(time.minutes)}:{formatTime(time.seconds)}
                </p>
            </div>
        </div>
    );
}
